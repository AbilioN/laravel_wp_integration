<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laravel WordPress Integration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: 600;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-shopping-cart me-2"></i>Minha Loja
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">
                            <i class="fas fa-box me-1"></i>Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart me-1"></i>Carrinho
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ $user->display_name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Minha Conta
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> {{ $user->display_name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Usuário:</strong> {{ $user->username }}</p>
                        
                        <div class="mt-3">
                            <a href="http://localhost:8080/my-account" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Gerenciar Conta no WordPress
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-bag me-2"></i>Meus Pedidos
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Visualize e gerencie seus pedidos no WordPress.</p>
                        
                        <div class="d-grid gap-2">
                            <a href="http://localhost:8080/my-account/orders" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-list me-1"></i>Ver Pedidos
                            </a>
                            <a href="http://localhost:8080/my-account/downloads" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-download me-1"></i>Downloads
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Carrinho
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Gerencie seus produtos no carrinho.</p>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-shopping-cart me-1"></i>Ver Carrinho
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-plus me-1"></i>Adicionar Produtos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>Ações Rápidas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-box me-1"></i>Ver Produtos
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('cart.index') }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-shopping-cart me-1"></i>Meu Carrinho
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="http://localhost:8080/my-account/edit-account" target="_blank" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-edit me-1"></i>Editar Conta
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="http://localhost:8080/my-account/edit-address" target="_blank" class="btn btn-outline-info w-100">
                                    <i class="fas fa-map-marker-alt me-1"></i>Endereços
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 