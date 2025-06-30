<?php

$password = 'password';
$hash = '$wp$2y$10$/ktG/bzsEU/Ag7umrbqsTeiSLAJ4IXWjA5ZaN/ZBjqQZ.oNDEP1Vm';

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "Hash type: " . (strpos($hash, '$wp$') === 0 ? 'WordPress Custom' : 'Standard') . "\n";

// Remover prefixo $wp$
if (strpos($hash, '$wp$') === 0) {
    $clean_hash = substr($hash, 4);
    echo "Clean hash: $clean_hash\n";
    
    $result = password_verify($password, $clean_hash);
    echo "password_verify result: " . ($result ? 'true' : 'false') . "\n";
    
    // Testar com hash original também
    $result2 = password_verify($password, $hash);
    echo "password_verify with original hash: " . ($result2 ? 'true' : 'false') . "\n";
}

// Testar com um hash bcrypt válido para comparação
$test_hash = password_hash('password', PASSWORD_BCRYPT);
echo "\nTest hash: $test_hash\n";
$test_result = password_verify('password', $test_hash);
echo "Test password_verify: " . ($test_result ? 'true' : 'false') . "\n";
?> 