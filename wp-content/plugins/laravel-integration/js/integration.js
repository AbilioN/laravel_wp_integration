jQuery(document).ready(function($) {
    
    // Interceptar cliques em links específicos
    $('a[href*="/checkout"], a[href*="/minha-conta"]').on('click', function(e) {
        e.preventDefault();
        
        var url = $(this).attr('href');
        
        // Redirecionar para URL do Laravel
        window.location.href = laravel_ajax.laravel_url + url;
    });
    
    // Exemplo de requisição AJAX para API do Laravel
    function getProductsFromLaravel() {
        $.ajax({
            url: laravel_ajax.laravel_url + '/api/produtos',
            method: 'GET',
            success: function(data) {
                // Manipular dados dos produtos
                console.log('Produtos do Laravel:', data);
            },
            error: function(err) {
                console.error('Erro ao buscar produtos:', err);
            }
        });
    }
    
    // Chamada de exemplo
    if ($('body').hasClass('woocommerce-shop')) {
        getProductsFromLaravel();
    }
    
}); 