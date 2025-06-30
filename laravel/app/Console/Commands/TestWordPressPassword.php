<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Corcel\Model\User as WordPressUser;

class TestWordPressPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:wp-password {username} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WordPress password verification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');

        $this->info("Testando senha para usuário: $username");

        // Buscar usuário usando o modelo User do Corcel
        $user = WordPressUser::where('user_login', $username)
            ->orWhere('user_email', $username)
            ->first();

        if (!$user) {
            $this->error('Usuário não encontrado!');
            return 1;
        }

        $this->info("Usuário encontrado: {$user->user_login}");
        $this->info("Hash: {$user->user_pass}");
        $this->info("Tipo de hash: " . (strpos($user->user_pass, '$wp$') === 0 ? 'WordPress Custom' : 'Standard'));

        // Testar verificação
        $authController = new \App\Http\Controllers\AuthController();
        $reflection = new \ReflectionClass($authController);
        $method = $reflection->getMethod('verifyWordPressPassword');
        $method->setAccessible(true);

        $result = $method->invoke($authController, $password, $user->user_pass);

        $this->info("Resultado da verificação: " . ($result ? 'SUCESSO' : 'FALHA'));

        return $result ? 0 : 1;
    }
} 