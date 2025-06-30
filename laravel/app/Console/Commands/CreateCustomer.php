<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:create {username} {email} {first_name} {last_name} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar um novo customer no WordPress';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        $email = $this->argument('email');
        $firstName = $this->argument('first_name');
        $lastName = $this->argument('last_name');
        $password = $this->argument('password');

        try {
            // Verificar se usuário já existe
            $existingUser = DB::connection('wordpress')
                ->table('users')
                ->where('user_login', $username)
                ->orWhere('user_email', $email)
                ->first();

            if ($existingUser) {
                $this->error("❌ Usuário já existe: $username ou $email");
                return 1;
            }

            // Criar hash da senha usando função do WordPress
            $passwordHash = $this->createWordPressPasswordHash($password);

            // Inserir usuário
            $userId = DB::connection('wordpress')
                ->table('users')
                ->insertGetId([
                    'user_login' => $username,
                    'user_pass' => $passwordHash,
                    'user_nicename' => strtolower(str_replace(' ', '-', $firstName . ' ' . $lastName)),
                    'user_email' => $email,
                    'user_status' => 0,
                    'display_name' => $firstName . ' ' . $lastName,
                    'user_registered' => now()
                ]);

            // Inserir meta dados
            $metaData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'nickname' => $firstName,
                'rich_editing' => 'true',
                'syntax_highlighting' => 'true',
                'comment_shortcuts' => 'false',
                'admin_color' => 'fresh',
                'use_ssl' => '0',
                'show_admin_bar_front' => 'true',
                'locale' => '',
                'wp_capabilities' => 'a:1:{s:8:"customer";b:1;}',
                'wp_user_level' => '0',
                'dismissed_wp_pointers' => '',
                'show_welcome_panel' => '1',
                'session_tokens' => '',
                'last_update' => time(),
                'billing_first_name' => $firstName,
                'billing_last_name' => $lastName,
                'billing_email' => $email,
                'shipping_first_name' => $firstName,
                'shipping_last_name' => $lastName
            ];

            foreach ($metaData as $metaKey => $metaValue) {
                DB::connection('wordpress')
                    ->table('usermeta')
                    ->insert([
                        'user_id' => $userId,
                        'meta_key' => $metaKey,
                        'meta_value' => $metaValue
                    ]);
            }

            $this->info("✅ Customer criado com sucesso!");
            $this->info("   ID: $userId");
            $this->info("   Usuário: $username");
            $this->info("   Email: $email");
            $this->info("   Nome: $firstName $lastName");
            $this->info("   Senha: $password");
            $this->info("");
            $this->info("🔗 Agora você pode fazer login em:");
            $this->info("   Laravel: http://localhost:8005/login");
            $this->info("   WordPress: http://localhost:8080/wp-login.php");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erro ao criar customer: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Criar hash da senha usando função do WordPress
     */
    private function createWordPressPasswordHash($password)
    {
        // Criar script temporário para usar função do WordPress
        $script = "<?php
require_once('/var/www/html/wp-config.php');
require_once('/var/www/html/wp-includes/pluggable.php');

\$password = '" . addslashes($password) . "';
\$hash = wp_hash_password(\$password);
echo \$hash;
";
        
        $tempFile = storage_path('temp_wp_hash.php');
        file_put_contents($tempFile, $script);
        
        // Executar no container WordPress
        $command = "docker-compose exec -T wordpress php " . $tempFile;
        $output = shell_exec($command);
        
        // Limpar arquivo
        unlink($tempFile);
        
        return trim($output);
    }
}
