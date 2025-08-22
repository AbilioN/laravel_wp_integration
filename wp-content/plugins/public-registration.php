<?php
/**
 * Plugin Name: Public Customer Registration
 * Description: Endpoint público para registro de clientes
 * Version: 1.0
 * Author: Your Name
 */

// Prevenir acesso direto
if (!defined('ABSPATH')) {
    exit;
}

// Adicionar endpoint de registro público
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/register', array(
        'methods' => 'POST',
        'callback' => 'handle_public_registration',
        'permission_callback' => '__return_true', // Público
        'args' => array(
            'username' => array(
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_user',
            ),
            'email' => array(
                'required' => true,
                'type' => 'string',
                'format' => 'email',
                'sanitize_callback' => 'sanitize_email',
            ),
            'password' => array(
                'required' => true,
                'type' => 'string',
                'minLength' => 6,
            ),
            'first_name' => array(
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'last_name' => array(
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'phone' => array(
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
});

function handle_public_registration($request) {
    $username = $request->get_param('username');
    $email = $request->get_param('email');
    $password = $request->get_param('password');
    $first_name = $request->get_param('first_name');
    $last_name = $request->get_param('last_name');
    $phone = $request->get_param('phone');

    // Verificar se username já existe
    if (username_exists($username)) {
        return new WP_Error(
            'username_exists',
            'Este nome de usuário já está em uso.',
            array('status' => 400)
        );
    }

    // Verificar se email já existe
    if (email_exists($email)) {
        return new WP_Error(
            'email_exists',
            'Este email já está em uso.',
            array('status' => 400)
        );
    }

    // Criar o usuário
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        return new WP_Error(
            'user_creation_failed',
            'Erro ao criar usuário: ' . $user_id->get_error_message(),
            array('status' => 500)
        );
    }

    // Definir role como "customer"
    $user = new WP_User($user_id);
    $user->set_role('customer');

    // Atualizar dados do usuário
    wp_update_user(array(
        'ID' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
    ));

    // Criar/atualizar cliente WooCommerce
    $customer_data = array(
        'user_id' => $user_id,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'billing' => array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
        ),
        'shipping' => array(
            'first_name' => $first_name,
            'last_name' => $last_name,
        ),
    );

    // Se WooCommerce estiver ativo, criar cliente
    if (class_exists('WC_Customer')) {
        $customer = new WC_Customer($user_id);
        $customer->set_first_name($first_name);
        $customer->set_last_name($last_name);
        $customer->set_email($email);
        $customer->set_billing_phone($phone);
        $customer->save();
    }

    // Retornar dados do usuário criado (sem senha)
    return array(
        'success' => true,
        'message' => 'Usuário criado com sucesso!',
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'role' => 'customer',
    );
}

// Adicionar CORS para o endpoint
add_action('rest_api_init', function () {
    add_filter('rest_pre_serve_request', function ($served, $result, $request, $server) {
        if ($request->get_route() === '/custom/v1/register') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
        }
        return $served;
    }, 10, 4);
}); 