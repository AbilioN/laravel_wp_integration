<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteTitle }} - Laravel WordPress</title>
    <meta name="description" content="{{ $siteDescription }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }
        .page-content {
            line-height: 1.8;
            font-size: 1.1em;
        }
        .page-content img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
        }
        .post-card {
            transition: transform 0.2s;
        }
        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .main-content {
            margin-top: 2rem;
        }
        .sidebar {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1.5rem;
        }
        .site-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .site-description {
            font-size: 1.2rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- WordPress Dynamic Navbar -->
    @include('components.wordpress-navbar')

    @if($viewType === 'page' && $homePage)
        <!-- Página inicial baseada em uma página específica -->
        <div class="container main-content">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Conteúdo da página inicial -->
                    <article>
                        <header class="mb-4">
                            <h1 class="display-4">
                                <i class="fas fa-home me-2"></i>{{ $homePage->post_title }}
                            </h1>
                            
                            @if($homePage->post_excerpt)
                                <div class="lead mb-4">
                                    <i class="fas fa-quote-left me-2"></i>{{ $homePage->post_excerpt }}
                                </div>
                            @endif
                        </header>

                        <div class="page-content">
                            {!! $homePage->post_content !!}
                        </div>
                    </article>
                </div>

                <div class="col-lg-4">
                    <!-- Sidebar com posts recentes -->
                    <div class="sidebar">
                        <h4><i class="fas fa-newspaper me-2"></i>Posts Recentes</h4>
                        
                        @if($posts->count() > 0)
                            @foreach($posts as $post)
                                <div class="card post-card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="http://wordpress.local/?p={{ $post->ID }}" 
                                               target="_blank" class="text-decoration-none">
                                                {{ Str::limit($post->post_title, 50) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($post->post_date)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                            
                            <div class="text-center">
                                <a href="{{ route('wordpress.posts.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-1"></i>Ver todos os posts
                                </a>
                            </div>
                        @else
                            <p class="text-muted">Nenhum post encontrado.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Página inicial baseada em posts -->
        <div class="hero-section">
            <div class="container text-center">
                <h1 class="site-title">{{ $siteTitle }}</h1>
                @if($siteDescription)
                    <p class="site-description">{{ $siteDescription }}</p>
                @endif
                <div class="mt-4">
                    <a href="{{ route('wordpress.pages.index') }}" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-file-alt me-1"></i>Ver Páginas
                    </a>
                    <a href="{{ route('wordpress.posts.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-newspaper me-1"></i>Ver Posts
                    </a>
                </div>
            </div>
        </div>

        <div class="container main-content">
            <h2 class="mb-4">
                <i class="fas fa-newspaper me-2"></i>Posts Recentes
            </h2>
            
            @if($posts->count() > 0)
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card post-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="http://wordpress.local/?p={{ $post->ID }}" 
                                           target="_blank" class="text-decoration-none">
                                            <i class="fas fa-newspaper me-1"></i>{{ $post->post_title }}
                                        </a>
                                    </h5>
                                    
                                    @if($post->post_excerpt)
                                        <p class="card-text text-muted">
                                            {{ Str::limit($post->post_excerpt, 150) }}
                                        </p>
                                    @else
                                        <p class="card-text text-muted">
                                            {{ Str::limit(strip_tags($post->post_content), 150) }}
                                        </p>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ \Carbon\Carbon::parse($post->post_date)->format('d/m/Y') }}
                                        </small>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-newspaper me-1"></i>Post
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="http://wordpress.local/?p={{ $post->ID }}" 
                                       target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Ler no WordPress
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('wordpress.posts.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-list me-1"></i>Ver todos os posts
                    </a>
                </div>
            @else
                <div class="alert alert-info">
                    <h4><i class="fas fa-info-circle me-2"></i>Nenhum post encontrado</h4>
                    <p>Não há posts publicados no WordPress.</p>
                </div>
            @endif
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 