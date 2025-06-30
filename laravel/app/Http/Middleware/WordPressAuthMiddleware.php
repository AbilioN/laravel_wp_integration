<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\WordPressSettings;
use Symfony\Component\HttpFoundation\Response;

class WordPressAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se já está autenticado no Laravel, verificar se precisa sincronizar
        if (Auth::check()) {
            $laravelUser = Auth::user();
            
            // Se é um usuário do WordPress, sincronizar dados
            if ($laravelUser->isWordPressUser()) {
                $laravelUser->syncWithWordPress();
            }
        } else {
            // Se não está autenticado no Laravel, verificar se está logado no WordPress
            $wordpressUser = $this->getCurrentWordPressUser();
            
            if ($wordpressUser) {
                // Criar ou atualizar usuário no Laravel
                $laravelUser = User::updateOrCreate(
                    ['email' => $wordpressUser->user_email],
                    [
                        'name' => $wordpressUser->display_name ?: $wordpressUser->user_login,
                        'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                        'wordpress_id' => $wordpressUser->ID,
                        'wordpress_username' => $wordpressUser->user_login
                    ]
                );
                
                // Fazer login no Laravel
                Auth::login($laravelUser);
            }
        }

        return $next($request);
    }

    /**
     * Buscar usuário atual do WordPress
     */
    private function getCurrentWordPressUser()
    {
        try {
            // Verificar cookie de sessão do WordPress
            $cookieName = 'wordpress_logged_in_' . md5(WordPressSettings::getWordPressUrl());
            
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
} 