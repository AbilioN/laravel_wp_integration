<?php
/**
 * Gateway de Pagamento Simulado para Demonstração
 * 
 * Este gateway permite testar todo o fluxo do e-commerce
 * sem precisar de um gateway real de pagamento.
 */

// Adicionar gateway à lista de gateways disponíveis
add_filter('woocommerce_payment_gateways', 'add_demo_gateway');

function add_demo_gateway($gateways) {
    $gateways[] = 'WC_Gateway_Demo';
    return $gateways;
}

// Classe do Gateway de Demonstração
class WC_Gateway_Demo extends WC_Payment_Gateway {
    
    public function __construct() {
        $this->id = 'demo_gateway';
        $this->icon = '';
        $this->has_fields = false;
        $this->method_title = 'Gateway de Demonstração';
        $this->method_description = 'Permite testar o fluxo completo sem pagamento real';
        
        // Configurações
        $this->init_form_fields();
        $this->init_settings();
        
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');
        
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }
    
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Habilitar/Desabilitar',
                'type' => 'checkbox',
                'label' => 'Habilitar Gateway de Demonstração',
                'default' => 'yes'
            ),
            'title' => array(
                'title' => 'Título',
                'type' => 'text',
                'description' => 'Título que o cliente verá durante o checkout',
                'default' => 'Pagamento de Demonstração',
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => 'Descrição',
                'type' => 'textarea',
                'description' => 'Descrição que o cliente verá durante o checkout',
                'default' => 'Este é um gateway de demonstração. Nenhum pagamento real será processado.',
                'desc_tip' => true,
            )
        );
    }
    
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);
        
        // Simular processamento
        sleep(1);
        
        // Marcar pagamento como completo
        $order->payment_complete();
        
        // Adicionar nota ao pedido
        $order->add_order_note('Pagamento simulado processado com sucesso via Gateway de Demonstração');
        
        // Limpar carrinho
        WC()->cart->empty_cart();
        
        // Retornar sucesso
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($order)
        );
    }
}

// Adicionar botão de simulação no admin
add_action('woocommerce_admin_order_data_after_billing_address', 'add_demo_payment_button');

function add_demo_payment_button($order) {
    if ($order->get_payment_method() === 'demo_gateway' && $order->get_status() === 'pending') {
        echo '<p><a href="' . admin_url('admin-ajax.php?action=simulate_payment&order_id=' . $order->get_id()) . '" class="button button-primary">Simular Pagamento</a></p>';
    }
}

// Processar simulação de pagamento
add_action('wp_ajax_simulate_payment', 'process_simulate_payment');

function process_simulate_payment() {
    $order_id = $_GET['order_id'];
    $order = wc_get_order($order_id);
    
    if ($order && $order->get_payment_method() === 'demo_gateway') {
        $order->payment_complete();
        $order->add_order_note('Pagamento simulado via admin');
        
        wp_redirect(admin_url('post.php?post=' . $order_id . '&action=edit'));
        exit;
    }
}

// Adicionar campo de cartão simulado no checkout
add_action('woocommerce_after_checkout_billing_form', 'add_demo_card_fields');

function add_demo_card_fields($checkout) {
    if (WC()->payment_gateways()->get_available_payment_gateways()['demo_gateway']) {
        echo '<div id="demo-card-fields" style="display: none;">';
        echo '<h3>Dados do Cartão (Demonstração)</h3>';
        echo '<p class="form-row form-row-wide">';
        echo '<label>Número do Cartão</label>';
        echo '<input type="text" value="4242 4242 4242 4242" readonly />';
        echo '</p>';
        echo '<p class="form-row form-row-first">';
        echo '<label>Data de Validade</label>';
        echo '<input type="text" value="12/25" readonly />';
        echo '</p>';
        echo '<p class="form-row form-row-last">';
        echo '<label>CVV</label>';
        echo '<input type="text" value="123" readonly />';
        echo '</p>';
        echo '</div>';
        
        echo '<script>
        jQuery(document).ready(function($) {
            $("input[name=payment_method]").on("change", function() {
                if ($(this).val() === "demo_gateway") {
                    $("#demo-card-fields").show();
                } else {
                    $("#demo-card-fields").hide();
                }
            });
        });
        </script>';
    }
} 