@extends('layouts.pwa')

@section('title', 'Minha Conta - WordPress Laravel Integration')
@section('description', 'Gerenciar sua conta e pedidos')

@section('content')
    @include('components.wordpress-navbar')

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Minha Conta</h1>
                
                @if($isLoggedIn)
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Informações da Conta
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nome:</strong> {{ $currentUser->display_name ?: $currentUser->user_login }}</p>
                                            <p><strong>Email:</strong> {{ $currentUser->user_email }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Usuário:</strong> {{ $currentUser->user_login }}</p>
                                            <p><strong>Membro desde:</strong> {{ \Carbon\Carbon::parse($currentUser->user_registered)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Meus Pedidos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        Para ver seus pedidos completos, acesse o WordPress:
                                    </p>
                                    <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account" 
                                       target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        Acessar Minha Conta no WordPress
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-cog me-2"></i>
                                        Ações Rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/orders" 
                                           target="_blank" class="btn btn-outline-primary">
                                            <i class="fas fa-list me-1"></i>
                                            Ver Pedidos
                                        </a>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/edit-account" 
                                           target="_blank" class="btn btn-outline-secondary">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar Conta
                                        </a>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/my-account/edit-address" 
                                           target="_blank" class="btn btn-outline-info">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            Endereços
                                        </a>
                                        <a href="{{ route('wordpress.logout') }}" 
                                           class="btn btn-outline-danger">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            Sair
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <div class="alert alert-warning">
                            <h4><i class="fas fa-exclamation-triangle me-2"></i>Não logado</h4>
                            <p>Você precisa estar logado para acessar esta página.</p>
                            <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/wp-login.php" 
                               target="_blank" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Fazer Login
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 