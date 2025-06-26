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
            
            // Verificar se o usuário está logado no WordPress
            $currentUser = $this->getCurrentWordPressUser();
            $isLoggedIn = !is_null($currentUser);
            
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

    /**
     * Processar login do WordPress
     */
    public function processWordPressLogin(Request $request)
    {
        try {
            $username = $request->input('log');
            $password = $request->input('pwd');
            $remember = $request->has('rememberme');
            
            // Conectar ao banco do WordPress
            $wordpressDb = DB::connection('wordpress');
            
            // Buscar usuário
            $user = $wordpressDb->table('users')
                ->where('user_login', $username)
                ->orWhere('user_email', $username)
                ->first();
            
            if (!$user) {
                return back()->withErrors(['username' => 'Usuário não encontrado']);
            }
            
            // Verificar senha usando hash do WordPress
            if (!$this->wp_check_password($password, $user->user_pass)) {
                return back()->withErrors(['password' => 'Senha incorreta']);
            }
            
            // Criar sessão do WordPress
            $this->createWordPressSession($user, $remember);
            
            // Criar sessão do Laravel
            $this->createLaravelSession($user);
            
            return redirect()->intended('/wordpress/pages/my-account');
            
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Erro ao fazer login: ' . $e->getMessage()]);
        }
    }

    /**
     * Processar logout
     */
    public function processWordPressLogout(Request $request)
    {
        try {
            // Destruir sessão do WordPress
            $this->destroyWordPressSession();
            
            // Destruir sessão do Laravel
            $this->destroyLaravelSession();
            
            return redirect('/')->with('success', 'Logout realizado com sucesso');
            
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Erro ao fazer logout');
        }
    }

    /**
     * Buscar usuário atual do WordPress
     */
    private function getCurrentWordPressUser()
    {
        try {
            // Verificar cookie de sessão do WordPress
            $cookieName = 'wordpress_logged_in_' . md5($this->getSiteUrl());
            
            if (!isset($_COOKIE[$cookieName])) {
                return null;
            }
            
            $cookieValue = $_COOKIE[$cookieName];
            $parts = explode('|', $cookieValue);
            
            if (count($parts) !== 2) {
                return null;
            }
            
            $username = $parts[0];
            $expiration = $parts[1];
            
            // Verificar se o cookie expirou
            if (time() > $expiration) {
                return null;
            }
            
            // Buscar usuário no banco
            $wordpressDb = DB::connection('wordpress');
            $user = $wordpressDb->table('users')
                ->where('user_login', $username)
                ->first();
            
            return $user;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Criar sessão do WordPress
     */
    private function createWordPressSession($user, $remember = false)
    {
        try {
            $wordpressDb = DB::connection('wordpress');
            
            // Gerar token de sessão
            $sessionToken = $this->wp_generate_password(32, false);
            
            // Calcular expiração
            $expiration = $remember ? time() + (30 * 86400) : time() + (2 * 86400); // 30 dias ou 2 dias
            
            // Salvar sessão no banco
            $wordpressDb->table('usermeta')->insert([
                'user_id' => $user->ID,
                'meta_key' => 'session_tokens',
                'meta_value' => serialize([$sessionToken => [
                    'expiration' => $expiration,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'ua' => $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]])
            ]);
            
            // Definir cookie
            $cookieName = 'wordpress_logged_in_' . md5($this->getSiteUrl());
            $cookieValue = $user->user_login . '|' . $expiration;
            
            setcookie($cookieName, $cookieValue, $expiration, '/', '', $this->is_ssl(), true);
            
        } catch (\Exception $e) {
            throw new \Exception('Erro ao criar sessão do WordPress: ' . $e->getMessage());
        }
    }

    /**
     * Criar sessão do Laravel
     */
    private function createLaravelSession($user)
    {
        try {
            // Criar ou atualizar usuário no Laravel
            $laravelUser = \App\Models\User::updateOrCreate(
                ['email' => $user->user_email],
                [
                    'name' => $user->display_name ?: $user->user_login,
                    'password' => bcrypt(\Illuminate\Support\Str::random(16)), // Senha aleatória para Laravel
                    'wordpress_id' => $user->ID,
                    'wordpress_username' => $user->user_login
                ]
            );
            
            // Fazer login no Laravel
            \Illuminate\Support\Facades\Auth::login($laravelUser);
            
        } catch (\Exception $e) {
            throw new \Exception('Erro ao criar sessão do Laravel: ' . $e->getMessage());
        }
    }

    /**
     * Destruir sessão do WordPress
     */
    private function destroyWordPressSession()
    {
        try {
            $cookieName = 'wordpress_logged_in_' . md5($this->getSiteUrl());
            
            if (isset($_COOKIE[$cookieName])) {
                setcookie($cookieName, '', time() - 3600, '/', '', $this->is_ssl(), true);
            }
            
        } catch (\Exception $e) {
            // Ignorar erros ao destruir sessão
        }
    }

    /**
     * Destruir sessão do Laravel
     */
    private function destroyLaravelSession()
    {
        try {
            \Illuminate\Support\Facades\Auth::logout();
        } catch (\Exception $e) {
            // Ignorar erros ao destruir sessão
        }
    }

    /**
     * Função auxiliar para verificar senha do WordPress
     */
    private function wp_check_password($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Função auxiliar para gerar senha do WordPress
     */
    private function wp_generate_password($length = 12, $special_chars = true)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($special_chars) {
            $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
        }
        
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }

    /**
     * Função auxiliar para obter URL do site
     */
    private function getSiteUrl()
    {
        return WordPressSettings::getWordPressUrl();
    }

    /**
     * Função auxiliar para verificar se é HTTPS
     */
    private function is_ssl()
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    }
}
