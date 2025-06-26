<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - Laravel WordPress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .redirect-container {
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .redirect-card {
            text-align: center;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <!-- WordPress Dynamic Navbar -->
    @include('components.wordpress-navbar')

    <div class="container">
        <div class="redirect-container">
            <div class="redirect-card">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-user-circle me-2"></i>Minha Conta
                        </h3>
                        
                        <p class="card-text mb-4">
                            Você será redirecionado para a página "Minha Conta" do WordPress para acessar todas as funcionalidades do WooCommerce.
                        </p>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account" 
                               class="btn btn-primary btn-lg">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Ir para Minha Conta
                            </a>
                            
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>
                                Voltar ao Início
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Redirecionamento automático após 3 segundos
        setTimeout(function() {
            window.location.href = '{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account';
        }, 3000);
    </script>
</body>
</html> 