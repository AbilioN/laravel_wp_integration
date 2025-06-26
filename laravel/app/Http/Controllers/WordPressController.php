<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WordPressPost;
use App\Models\WordPressMenu;
use Illuminate\Support\Facades\Cache;
use App\Models\WordPressSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WordPressController extends Controller
{
    /**
     * Exibir lista de todas as páginas
     */
    public function index()
    {
        $pages = WordPressPost::getPages();
        
        return view('wordpress.pages.index', compact('pages'));
    }

    /**
     * Exibir uma página específica por slug
     */
    public function show($slug)
    {
        $page = WordPressPost::getPageBySlug($slug);
        
        if (!$page) {
            abort(404, 'Página não encontrada');
        }

        // Buscar meta dados da página
        $meta = $page->getAllMeta();
        
        return view('wordpress.pages.show', compact('page', 'meta'));
    }

    /**
     * Exibir uma página por ID
     */
    public function showById($id)
    {
        $page = WordPressPost::getPageById($id);
        
        if (!$page) {
            abort(404, 'Página não encontrada');
        }

        $meta = $page->getAllMeta();
        
        return view('wordpress.pages.show', compact('page', 'meta'));
    }

    /**
     * Exibir lista de posts
     */
    public function posts(Request $request)
    {
        $limit = $request->get('limit', 10);
        $posts = WordPressPost::getPosts($limit);
        
        return view('wordpress.posts.index', compact('posts'));
    }

    /**
     * API para buscar páginas
     */
    public function apiPages()
    {
        $pages = WordPressPost::getPages();
        
        return response()->json([
            'success' => true,
            'data' => $pages
        ]);
    }

    /**
     * API para buscar uma página específica
     */
    public function apiPage($slug)
    {
        $page = WordPressPost::getPageBySlug($slug);
        
        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Página não encontrada'
            ], 404);
        }

        $meta = $page->getAllMeta();
        
        return response()->json([
            'success' => true,
            'data' => [
                'page' => $page,
                'meta' => $meta
            ]
        ]);
    }

    /**
     * Buscar páginas por termo
     */
    public function search(Request $request)
    {
        $term = $request->get('q');
        
        if (!$term) {
            return redirect()->route('wordpress.pages.index');
        }

        $pages = WordPressPost::where('post_type', 'page')
                             ->where('post_status', 'publish')
                             ->where(function($query) use ($term) {
                                 $query->where('post_title', 'like', "%{$term}%")
                                       ->orWhere('post_content', 'like', "%{$term}%");
                             })
                             ->orderBy('post_title', 'asc')
                             ->get();

        return view('wordpress.pages.search', compact('pages', 'term'));
    }

    /**
     * API para buscar dados da navbar
     */
    public function apiNavbar()
    {
        $navbarData = Cache::remember('wordpress_navbar', 300, function () {
            return [
                'pages' => WordPressMenu::getNavigationPages(),
                'recentPosts' => WordPressMenu::getRecentPosts(3)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $navbarData
        ]);
    }

    /**
     * Limpar cache da navbar
     */
    public function clearNavbarCache()
    {
        Cache::forget('wordpress_navbar');
        
        return response()->json([
            'success' => true,
            'message' => 'Cache da navbar limpo com sucesso'
        ]);
    }

    /**
     * Buscar o conteúdo do shortcode My Account do WordPress
     */
    public function myAccount()
    {
        try {
            // Conectar ao banco do WordPress
            $wordpressDb = DB::connection('wordpress');
            
            // Buscar a página My Account
            $myAccountPage = $wordpressDb->table('wp_posts')
                ->where('post_type', 'page')
                ->where('post_status', 'publish')
                ->where('post_content', 'like', '%woocommerce_my_account%')
                ->first();
            
            if (!$myAccountPage) {
                // Se não encontrar a página, redirecionar para WordPress
                $wordpressUrl = WordPressSettings::getWordPressUrl();
                return redirect($wordpressUrl . '/my-account');
            }
            
            // Buscar o conteúdo processado (se disponível)
            $content = $myAccountPage->post_content;
            
            // Se contém o shortcode, redirecionar para WordPress
            if (strpos($content, 'woocommerce_my_account') !== false) {
                $wordpressUrl = WordPressSettings::getWordPressUrl();
                return redirect($wordpressUrl . '/my-account');
            }
            
            return view('wordpress.my-account', compact('content'));
            
        } catch (\Exception $e) {
            // Em caso de erro, redirecionar para WordPress
            $wordpressUrl = WordPressSettings::getWordPressUrl();
            return redirect($wordpressUrl . '/my-account');
        }
    }

    /**
     * Exibir a página My Account do WordPress com conteúdo processado
     */
    public function showMyAccountPage()
    {
        try {
            // Conectar ao banco do WordPress
            $wordpressDb = DB::connection('wordpress');
            
            // Buscar a página My Account
            $myAccountPage = $wordpressDb->table('posts')
                ->where('post_type', 'page')
                ->where('post_status', 'publish')
                ->where('post_content', 'like', '%woocommerce_my_account%')
                ->first();
            
            if (!$myAccountPage) {
                abort(404, 'Página My Account não encontrada no WordPress');
            }
            
            // Buscar dados do usuário atual (se autenticado no WordPress)
            $currentUser = null;
            $isLoggedIn = false;
            
            // Verificar se há cookie de sessão do WordPress
            if (isset($_COOKIE['wordpress_logged_in_'])) {
                $isLoggedIn = true;
                // Aqui você pode buscar dados do usuário se necessário
            }
            
            // Buscar dados básicos da página
            $pageData = [
                'ID' => $myAccountPage->ID,
                'post_title' => $myAccountPage->post_title,
                'post_content' => $myAccountPage->post_content,
                'post_date' => $myAccountPage->post_date,
                'post_modified' => $myAccountPage->post_modified,
                'post_name' => $myAccountPage->post_name
            ];
            
            // Buscar meta dados da página
            $metaData = $wordpressDb->table('postmeta')
                ->where('post_id', $myAccountPage->ID)
                ->get()
                ->keyBy('meta_key');
            
            return view('wordpress.pages.my-account', compact('pageData', 'metaData', 'isLoggedIn', 'currentUser'));
            
        } catch (\Exception $e) {
            abort(500, 'Erro ao carregar página My Account: ' . $e->getMessage());
        }
    }

    /**
     * API para buscar dados do My Account (se necessário)
     */
    public function myAccountApi()
    {
        try {
            $wordpressDb = DB::connection('wordpress');
            
            return response()->json([
                'message' => 'My Account API endpoint',
                'wordpress_url' => WordPressSettings::getWordPressUrl(),
                'note' => 'Redirect to WordPress for full functionality'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch account data',
                'wordpress_url' => WordPressSettings::getWordPressUrl()
            ], 500);
        }
    }
}
