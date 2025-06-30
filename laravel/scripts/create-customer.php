<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Configurar conexão com o banco
$config = [
    'driver' => 'mysql',
    'host' => 'db',
    'port' => '3306',
    'database' => 'wordpress',
    'username' => 'wordpress',
    'password' => 'wordpress',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

// Criar conexão
$pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}",
    $config['username'],
    $config['password']
);

// Função para criar customer
function createCustomer($username, $email, $firstName, $lastName, $password) {
    global $pdo;
    
    // Verificar se usuário já existe
    $stmt = $pdo->prepare("SELECT ID FROM wp_users WHERE user_login = ? OR user_email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        echo "❌ Usuário já existe: $username\n";
        return false;
    }
    
    // Criar hash da senha
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Inserir usuário
    $stmt = $pdo->prepare("
        INSERT INTO wp_users (user_login, user_pass, user_nicename, user_email, user_status, display_name, user_registered)
        VALUES (?, ?, ?, ?, 0, ?, NOW())
    ");
    
    $displayName = trim($firstName . ' ' . $lastName);
    $nicename = strtolower(str_replace(' ', '-', $displayName));
    
    $stmt->execute([$username, $passwordHash, $nicename, $email, $displayName]);
    $userId = $pdo->lastInsertId();
    
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
        $stmt = $pdo->prepare("INSERT INTO wp_usermeta (user_id, meta_key, meta_value) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $metaKey, $metaValue]);
    }
    
    echo "✅ Customer criado com sucesso!\n";
    echo "   ID: $userId\n";
    echo "   Usuário: $username\n";
    echo "   Email: $email\n";
    echo "   Nome: $displayName\n";
    echo "   Senha: $password\n\n";
    
    return true;
}

// Lista de customers para criar
$customers = [
    [
        'username' => 'maria.silva',
        'email' => 'maria.silva@email.com',
        'firstName' => 'Maria',
        'lastName' => 'Silva',
        'password' => 'senha123'
    ],
    [
        'username' => 'joao.santos',
        'email' => 'joao.santos@email.com',
        'firstName' => 'João',
        'lastName' => 'Santos',
        'password' => 'senha123'
    ],
    [
        'username' => 'ana.oliveira',
        'email' => 'ana.oliveira@email.com',
        'firstName' => 'Ana',
        'lastName' => 'Oliveira',
        'password' => 'senha123'
    ]
];

echo "🚀 Criando customers no WordPress...\n\n";

foreach ($customers as $customer) {
    createCustomer(
        $customer['username'],
        $customer['email'],
        $customer['firstName'],
        $customer['lastName'],
        $customer['password']
    );
}

echo "✨ Processo concluído!\n";
echo "📝 Agora você pode fazer login no Laravel com qualquer um desses usuários.\n"; 