<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts - Laravel WordPress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .post-card {
            transition: transform 0.2s;
            margin-bottom: 2rem;
        }
        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .search-box {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .main-content {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- WordPress Dynamic Navbar -->
    @include('components.wordpress-navbar')

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-newspaper me-2"></i>Posts do WordPress
                </h1>

                <!-- Barra de pesquisa -->
                <div class="search-box">
                    <form method="GET" action="{{ route('wordpress.posts.index') }}" class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Buscar posts..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                        </div>
                    </form>
                </div>

                @if(request('search'))
                    <div class="alert alert-info">
                        <i class="fas fa-search me-2"></i>
                        Resultados para: <strong>"{{ request('search') }}"</strong>
                        <a href="{{ route('wordpress.posts.index') }}" class="btn btn-sm btn-outline-info ms-2">
                            <i class="fas fa-times me-1"></i>Limpar
                        </a>
                    </div>
                @endif

                @if($posts->count() > 0)
                    <div class="row">
                        @foreach($posts as $post)
                            <div class="col-md-6 col-lg-4">
                                <div class="card post-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/?p={{ $post->ID }}" 
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
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/?p={{ $post->ID }}" 
                                           target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>Ler no WordPress
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginação -->
                    @if($posts->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $posts->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-info text-center">
                        <h4><i class="fas fa-info-circle me-2"></i>Nenhum post encontrado</h4>
                        <p>Não há posts que correspondam aos critérios de busca.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 