<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WordPressPost;
use App\Models\WordPressMenu;
use Illuminate\Support\Facades\Cache;

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
}
