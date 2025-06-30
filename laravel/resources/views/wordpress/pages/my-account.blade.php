@extends('layouts.pwa')

@section('title', 'Minha Conta - WordPress Laravel Integration')
@section('description', 'Gerenciar sua conta e pedidos')

@section('content')
    @include('components.wordpress-navbar')

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

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
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Fazer Login
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('wordpress.login') }}">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="log" class="form-label">Usuário ou Email</label>
                                            <input type="text" 
                                                   class="form-control @error('log') is-invalid @enderror" 
                                                   id="log" 
                                                   name="log" 
                                                   value="{{ old('log') }}" 
                                                   required 
                                                   autocomplete="username">
                                            @error('log')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="pwd" class="form-label">Senha</label>
                                            <input type="password" 
                                                   class="form-control @error('pwd') is-invalid @enderror" 
                                                   id="pwd" 
                                                   name="pwd" 
                                                   required 
                                                   autocomplete="current-password">
                                            @error('pwd')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3 form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="rememberme" 
                                                   name="rememberme" 
                                                   value="1">
                                            <label class="form-check-label" for="rememberme">
                                                Lembrar de mim
                                            </label>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-sign-in-alt me-1"></i>
                                                Entrar
                                            </button>
                                        </div>
                                    </form>

                                    <hr class="my-4">

                                    <div class="text-center">
                                        <p class="text-muted mb-2">Ou acesse diretamente no WordPress:</p>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/wp-login.php" 
                                           target="_blank" 
                                           class="btn btn-outline-secondary">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            Login no WordPress
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 