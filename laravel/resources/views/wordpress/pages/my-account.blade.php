<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageData['post_title'] }} - Laravel WordPress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .woocommerce-MyAccount-navigation {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .woocommerce-MyAccount-navigation ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .woocommerce-MyAccount-navigation li {
            margin: 0;
        }
        .woocommerce-MyAccount-navigation a {
            color: #495057;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }
        .woocommerce-MyAccount-navigation a:hover {
            background-color: #e9ecef;
            color: #212529;
        }
        .woocommerce-MyAccount-navigation .is-active a {
            background-color: #007bff;
            color: white;
        }
        .woocommerce-MyAccount-content {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 2rem;
        }
        .login-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .account-dashboard {
            line-height: 1.6;
        }
        .account-dashboard p {
            margin-bottom: 1rem;
        }
        .account-dashboard a {
            color: #007bff;
            text-decoration: none;
        }
        .account-dashboard a:hover {
            text-decoration: underline;
        }
        .wordpress-redirect-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .user-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- WordPress Dynamic Navbar -->
    @include('components.wordpress-navbar')

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-user-circle me-2"></i>{{ $pageData['post_title'] }}
                </h1>

                @if(!$isLoggedIn)
                    <!-- Usuário não logado - Formulário de Login -->
                    <div class="wordpress-redirect-notice">
                        <h5><i class="fas fa-info-circle me-2"></i>Login Integrado</h5>
                        <p class="mb-2">Faça login usando suas credenciais do WordPress. Sua sessão será sincronizada entre Laravel e WordPress.</p>
                    </div>

                    <div class="woocommerce-MyAccount-content">
                        <div class="login-form">
                            <h3 class="mb-4">Login</h3>
                            
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <form method="post" action="{{ route('wordpress.login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nome de usuário ou email *</label>
                                    <input type="text" 
                                           class="form-control @error('log') is-invalid @enderror" 
                                           id="username" 
                                           name="log" 
                                           value="{{ old('log') }}"
                                           required>
                                    @error('log')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">Senha *</label>
                                    <input type="password" 
                                           class="form-control @error('pwd') is-invalid @enderror" 
                                           id="password" 
                                           name="pwd" 
                                           required>
                                    @error('pwd')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="rememberme" name="rememberme">
                                    <label class="form-check-label" for="rememberme">
                                        Lembrar de mim
                                    </label>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-1"></i>Entrar
                                    </button>
                                    
                                    <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account" class="btn btn-outline-secondary">
                                        <i class="fas fa-external-link-alt me-1"></i>Ir para WordPress
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Usuário logado - Dashboard da Conta -->
                    <div class="user-info">
                        <h5><i class="fas fa-user-check me-2"></i>Usuário Logado</h5>
                        <p class="mb-2">Bem-vindo, <strong>{{ $currentUser->display_name ?: $currentUser->user_login }}</strong>!</p>
                        <p class="mb-0">Sua sessão está sincronizada entre Laravel e WordPress.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <nav class="woocommerce-MyAccount-navigation">
                                <ul>
                                    <li class="is-active">
                                        <a href="#dashboard">
                                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/orders">
                                            <i class="fas fa-shopping-bag me-1"></i>Pedidos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/downloads">
                                            <i class="fas fa-download me-1"></i>Downloads
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/edit-address">
                                            <i class="fas fa-map-marker-alt me-1"></i>Endereços
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/payment-methods">
                                            <i class="fas fa-credit-card me-1"></i>Métodos de Pagamento
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/edit-account">
                                            <i class="fas fa-user-edit me-1"></i>Detalhes da Conta
                                        </a>
                                    </li>
                                    <li>
                                        <form method="post" action="{{ route('wordpress.logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 m-0 text-decoration-none">
                                                <i class="fas fa-sign-out-alt me-1"></i>Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        
                        <div class="col-md-9">
                            <div class="woocommerce-MyAccount-content">
                                <div class="account-dashboard">
                                    <h3>Dashboard</h3>
                                    
                                    <p>
                                        Olá <strong>{{ $currentUser->display_name ?: $currentUser->user_login }}</strong> (não é <strong>{{ $currentUser->display_name ?: $currentUser->user_login }}</strong>? 
                                        <form method="post" action="{{ route('wordpress.logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link p-0 m-0 text-decoration-none">Sair</button>
                                        </form>)
                                    </p>
                                    
                                    <p>
                                        Do painel da sua conta você pode ver seus 
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/orders">pedidos recentes</a>, 
                                        gerenciar seus 
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/edit-address">endereços de cobrança e entrega</a>, 
                                        e 
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/edit-account">editar sua senha e detalhes da conta</a>.
                                    </p>
                                    
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Sucesso!</strong> Você está logado no Laravel e WordPress simultaneamente.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 