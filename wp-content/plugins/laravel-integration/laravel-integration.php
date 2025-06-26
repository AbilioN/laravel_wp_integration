<?php
/**
 * Plugin Name: Laravel Integration
 * Description: Integra rotas específicas com aplicação Laravel
 * Version: 1.0
 * Author: Seu Nome
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class LaravelIntegration {
    
    private $laravel_url = 'http://laravel:8000';
    
    public function __construct() {
        add_action('init', array($this, 'check_laravel_routes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function check_laravel_routes() {
        $current_url = $_SERVER['REQUEST_URI'];
        
        // Rotas que devem ir para o Laravel
        $laravel_routes = array(
            '/api/',
            '/checkout/',
            '/minha-conta/',
            '/pedidos/',
            '/relatorios/',
            '/dashboard/',
            '/produtos-api/'
        );
        
        foreach ($laravel_routes as $route) {
            if (strpos($current_url, $route) === 0) {
                $this->proxy_to_laravel($current_url);
                exit;
            }
        }
    }
    
    private function proxy_to_laravel($url) {
        $laravel_url = $this->laravel_url . $url;
        
        // Configurar headers
        $headers = array(
            'Host: ' . $_SERVER['HTTP_HOST'],
            'X-Real-IP: ' . $_SERVER['REMOTE_ADDR'],
            'X-Forwarded-For: ' . $_SERVER['REMOTE_ADDR'],
            'X-Forwarded-Proto: ' . (isset($_SERVER['HTTPS']) ? 'https' : 'http')
        );
        
        // Fazer requisição para Laravel
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $laravel_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, $_SERVER['REQUEST_METHOD'] === 'POST');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Separar headers e body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        
        // Enviar headers
        foreach (explode("\n", $headers) as $header) {
            if (trim($header)) {
                header($header);
            }
        }
        
        // Enviar body
        echo $body;
    }
    
    public function enqueue_scripts() {
        // Adicionar JavaScript para interceptar certas ações
        wp_enqueue_script('laravel-integration', plugin_dir_url(__FILE__) . 'js/integration.js', array('jquery'), '1.0', true);
        
        // Passar variáveis para JavaScript
        wp_localize_script('laravel-integration', 'laravel_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'laravel_url' => $this->laravel_url
        ));
    }
}

// Inicializar plugin
new LaravelIntegration(); 