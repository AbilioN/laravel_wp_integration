<?php

// Teste simples da API
$baseUrl = 'http://localhost:8005/api/v1';

echo "=== Teste da API Headless WordPress + WooCommerce ===\n\n";

// Teste 1: Buscar páginas do WordPress
echo "1. Testando busca de páginas do WordPress...\n";
$response = file_get_contents($baseUrl . '/wordpress/pages');
if ($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "✅ Sucesso! Encontradas " . count($data['data']) . " páginas\n";
    } else {
        echo "❌ Erro na resposta da API\n";
    }
} else {
    echo "❌ Erro ao conectar com a API\n";
}

echo "\n";

// Teste 2: Buscar posts do WordPress
echo "2. Testando busca de posts do WordPress...\n";
$response = file_get_contents($baseUrl . '/wordpress/posts');
if ($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "✅ Sucesso! Encontrados " . count($data['data']) . " posts\n";
    } else {
        echo "❌ Erro na resposta da API\n";
    }
} else {
    echo "❌ Erro ao conectar com a API\n";
}

echo "\n";

// Teste 3: Buscar produtos do WooCommerce
echo "3. Testando busca de produtos do WooCommerce...\n";
$response = file_get_contents($baseUrl . '/woocommerce/products');
if ($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "✅ Sucesso! Encontrados " . count($data['data']) . " produtos\n";
    } else {
        echo "❌ Erro na resposta da API\n";
    }
} else {
    echo "❌ Erro ao conectar com a API\n";
}

echo "\n";

// Teste 4: Buscar categorias do WooCommerce
echo "4. Testando busca de categorias do WooCommerce...\n";
$response = file_get_contents($baseUrl . '/woocommerce/categories');
if ($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "✅ Sucesso! Encontradas " . count($data['data']) . " categorias\n";
    } else {
        echo "❌ Erro na resposta da API\n";
    }
} else {
    echo "❌ Erro ao conectar com a API\n";
}

echo "\n";

// Teste 5: Buscar carrinho
echo "5. Testando busca do carrinho...\n";
$response = file_get_contents($baseUrl . '/woocommerce/cart');
if ($response) {
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "✅ Sucesso! Carrinho encontrado com " . $data['data']['item_count'] . " itens\n";
    } else {
        echo "❌ Erro na resposta da API\n";
    }
} else {
    echo "❌ Erro ao conectar com a API\n";
}

echo "\n=== Teste concluído ===\n"; 