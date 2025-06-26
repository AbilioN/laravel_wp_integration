<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busca: {{ $term }} - Laravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
        .page-card {
            transition: transform 0.2s;
        }
        .page-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Navegação -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('wordpress.pages.index') }}">Páginas</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Busca</li>
                    </ol>
                </nav>

                <h1 class="mb-4">Resultados da Busca</h1>
                
                <!-- Barra de pesquisa -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="{{ route('wordpress.pages.search') }}" method="GET" class="d-flex">
                            <input type="text" name="q" class="form-control me-2" placeholder="Buscar páginas..." value="{{ $term }}">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('wordpress.pages.index') }}" class="btn btn-outline-secondary">Ver Todas as Páginas</a>
                    </div>
                </div>

                <!-- Resultados -->
                <div class="mb-3">
                    <p class="text-muted">
                        @if($pages->count() > 0)
                            Encontrados {{ $pages->count() }} resultado(s) para "<strong>{{ $term }}</strong>"
                        @else
                            Nenhum resultado encontrado para "<strong>{{ $term }}</strong>"
                        @endif
                    </p>
                </div>

                @if($pages->count() > 0)
                    <div class="row">
                        @foreach($pages as $page)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card page-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="{{ route('wordpress.pages.show', $page->post_name) }}" 
                                               class="text-decoration-none">
                                                {!! str_ireplace($term, '<span class="search-highlight">' . $term . '</span>', $page->post_title) !!}
                                            </a>
                                        </h5>
                                        
                                        @php
                                            $content = $page->post_excerpt ?: strip_tags($page->post_content);
                                            $content = Str::limit($content, 200);
                                            $content = str_ireplace($term, '<span class="search-highlight">' . $term . '</span>', $content);
                                        @endphp
                                        
                                        <p class="text-muted small">
                                            {!! $content !!}
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($page->post_date)->format('d/m/Y') }}
                                            </small>
                                            <span class="badge bg-primary">{{ $page->post_type }}</span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('wordpress.pages.show', $page->post_name) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            Ler mais
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <h4>Nenhum resultado encontrado</h4>
                        <p>Tente usar termos diferentes ou verificar a ortografia.</p>
                        <hr>
                        <a href="{{ route('wordpress.pages.index') }}" class="btn btn-primary">
                            Ver Todas as Páginas
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 