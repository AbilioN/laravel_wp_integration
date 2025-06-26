<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->post_title }} - Laravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
        .page-meta {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
        }
        .meta-item {
            margin-bottom: 0.5rem;
        }
        .meta-label {
            font-weight: bold;
            color: #495057;
        }
        .page-title {
            color: #333;
            text-decoration: none;
        }
        .page-title:hover {
            color: #007bff;
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
                <!-- Navegação -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('wordpress.pages.index') }}" class="page-title">
                                <i class="fas fa-home me-1"></i>Páginas
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-file-alt me-1"></i>{{ $page->post_title }}
                        </li>
                    </ol>
                </nav>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Conteúdo da página -->
                        <article>
                            <header class="mb-4">
                                <h1 class="display-5">
                                    <i class="fas fa-file-alt me-2"></i>{{ $page->post_title }}
                                </h1>
                                <div class="text-muted mb-3">
                                    <small>
                                        <i class="fas fa-calendar me-1"></i>
                                        <strong>Criado em:</strong> {{ \Carbon\Carbon::parse($page->post_date)->format('d/m/Y H:i') }}
                                        @if($page->post_modified != $page->post_date)
                                            | <i class="fas fa-edit me-1"></i>
                                            <strong>Atualizado em:</strong> {{ \Carbon\Carbon::parse($page->post_modified)->format('d/m/Y H:i') }}
                                        @endif
                                    </small>
                                </div>
                                
                                @if($page->post_excerpt)
                                    <div class="lead mb-4">
                                        <i class="fas fa-quote-left me-2"></i>{{ $page->post_excerpt }}
                                    </div>
                                @endif
                            </header>

                            <div class="page-content">
                                {!! $page->post_content !!}
                            </div>
                        </article>
                    </div>

                    <div class="col-lg-4">
                        <!-- Sidebar com informações -->
                        <div class="page-meta">
                            <h5><i class="fas fa-info-circle me-2"></i>Informações da Página</h5>
                            
                            <div class="meta-item">
                                <span class="meta-label"><i class="fas fa-hashtag me-1"></i>ID:</span> {{ $page->ID }}
                            </div>
                            
                            <div class="meta-item">
                                <span class="meta-label"><i class="fas fa-link me-1"></i>Slug:</span> {{ $page->post_name }}
                            </div>
                            
                            <div class="meta-item">
                                <span class="meta-label"><i class="fas fa-check-circle me-1"></i>Status:</span> 
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>{{ $page->post_status }}
                                </span>
                            </div>
                            
                            <div class="meta-item">
                                <span class="meta-label"><i class="fas fa-file me-1"></i>Tipo:</span> 
                                <span class="badge bg-primary">
                                    <i class="fas fa-file-alt me-1"></i>{{ $page->post_type }}
                                </span>
                            </div>

                            @if(!empty($meta))
                                <hr>
                                <h6><i class="fas fa-tags me-2"></i>Meta Dados</h6>
                                @foreach($meta as $key => $value)
                                    @if(!in_array($key, ['_edit_lock', '_edit_last']))
                                        <div class="meta-item">
                                            <span class="meta-label">{{ $key }}:</span>
                                            <div class="small text-break">{{ Str::limit($value, 100) }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <!-- Links úteis -->
                        <div class="mt-4">
                            <h5><i class="fas fa-compass me-2"></i>Navegação</h5>
                            <div class="d-grid gap-2">
                                <a href="{{ route('wordpress.pages.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i>Voltar para Páginas
                                </a>
                                <a href="{{ route('wordpress.posts.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-newspaper me-1"></i>Ver Posts
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