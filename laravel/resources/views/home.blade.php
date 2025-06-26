<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteTitle }}</title>
    <meta name="description" content="{{ $siteDescription }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
            margin-bottom: 2rem;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 1.5rem;
        }
        .post-title {
            color: #333;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .post-title:hover {
            color: #007bff;
        }
        .post-date {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .post-excerpt {
            color: #666;
            line-height: 1.6;
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
        @if($viewType === 'page' && $homePage)
            <!-- Página inicial baseada em uma página específica -->
            <div class="row">
                <div class="col-lg-8">
                    <!-- Conteúdo da página inicial -->
                    <article>
                        <header class="mb-4">
                            <h1>{{ $homePage->post_title }}</h1>
                            
                            @if($homePage->post_excerpt)
                                <div class="lead mb-4">
                                    {{ $homePage->post_excerpt }}
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
                    <div class="bg-light p-3 rounded">
                        <h4>Blog</h4>
                        
                        @if($posts->count() > 0)
                            @foreach($posts as $post)
                                <div class="mb-3">
                                    <h6>
                                        <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/?p={{ $post->ID }}" 
                                           target="_blank" class="text-decoration-none">
                                            {{ $post->post_title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($post->post_date)->format('F j, Y') }}
                                    </small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Nenhum post encontrado.</p>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <!-- Página inicial baseada em posts -->
            <div class="row">
                <div class="col-12">
                    @if($posts->count() > 0)
                        @foreach($posts as $post)
                            <article class="post-card">
                                <h2 class="post-title">
                                    <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/?p={{ $post->ID }}" 
                                       target="_blank">
                                        {{ $post->post_title }}
                                    </a>
                                </h2>
                                
                                <div class="post-date">
                                    {{ \Carbon\Carbon::parse($post->post_date)->format('F j, Y') }}
                                </div>
                                
                                <div class="post-excerpt">
                                    @if($post->post_excerpt)
                                        {{ $post->post_excerpt }}
                                    @else
                                        {{ Str::limit(strip_tags($post->post_content), 300) }}
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    @else
                        <div class="text-center">
                            <h3>Nenhum post encontrado</h3>
                            <p>Não há posts publicados no momento.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 