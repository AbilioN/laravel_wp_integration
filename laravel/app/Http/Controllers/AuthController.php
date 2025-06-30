<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mostrar formulário de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Processar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        try {
            // Buscar usuário no WordPress
            $user = DB::connection('wordpress')
                ->table('users')
                ->where('user_login', $username)
                ->orWhere('user_email', $username)
                ->first();
                dd($user);

            if (!$user) {
                return back()->withErrors(['username' => 'Usuário não encontrado']);
            }

            // Verificar senha usando função nativa do WordPress
            if (!$this->verifyWordPressPassword($password, $user->user_pass)) {
                return back()->withErrors(['password' => 'Senha incorreta']);
            }

            // Criar sessão
            $this->createSession($user);

            return redirect()->intended('/dashboard')->with('success', 'Login realizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro no login: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Erro ao fazer login']);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->destroySession();
        return redirect('/')->with('success', 'Logout realizado com sucesso!');
    }

    /**
     * Verificar senha do WordPress usando script externo
     */
    private function verifyWordPressPassword($password, $hash)
    {
        // Criar script temporário
        $script = "<?php
require_once('/var/www/html/wp-config.php');
require_once('/var/www/html/wp-includes/pluggable.php');

\$password = '" . addslashes($password) . "';
\$hash = '" . addslashes($hash) . "';

\$result = wp_check_password(\$password, \$hash);
echo \$result ? 'true' : 'false';
";
        
        $tempFile = storage_path('temp_wp_check.php');
        file_put_contents($tempFile, $script);
        
        // Executar no container WordPress
        $command = "docker-compose exec -T wordpress php " . $tempFile;
        $output = shell_exec($command);
        
        // Limpar arquivo
        unlink($tempFile);
        
        return trim($output) === 'true';
    }

    /**
     * Criar sessão do usuário
     */
    private function createSession($user)
    {
        // Salvar dados do usuário na sessão do Laravel
        session([
            'user_id' => $user->ID,
            'username' => $user->user_login,
            'email' => $user->user_email,
            'display_name' => $user->display_name ?: $user->user_login,
            'logged_in' => true
        ]);

        // Criar cookie compartilhado com WordPress
        $cookieName = 'wordpress_logged_in_' . md5('http://localhost');
        $cookieValue = $user->user_login . '|' . (time() + 86400 * 30); // 30 dias
        
        setcookie($cookieName, $cookieValue, time() + 86400 * 30, '/', '', false, true);
    }

    /**
     * Destruir sessão
     */
    private function destroySession()
    {
        session()->forget(['user_id', 'username', 'email', 'display_name', 'logged_in']);
        
        // Remover cookie do WordPress
        $cookieName = 'wordpress_logged_in_' . md5('http://localhost');
        setcookie($cookieName, '', time() - 3600, '/', '', false, true);
    }

    /**
     * Verificar se usuário está logado
     */
    public static function isLoggedIn()
    {
        return session('logged_in', false);
    }

    /**
     * Obter dados do usuário atual
     */
    public static function getCurrentUser()
    {
        if (!self::isLoggedIn()) {
            return null;
        }

        return (object) [
            'id' => session('user_id'),
            'username' => session('username'),
            'email' => session('email'),
            'display_name' => session('display_name')
        ];
    }
} 