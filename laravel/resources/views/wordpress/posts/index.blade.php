<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts do WordPress - Laravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .post-card {
            transition: transform 0.2s;
        }
        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .post-excerpt {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Posts do WordPress</h1>
                
                <!-- Controles -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form method="GET" class="d-flex">
                            <select name="limit" class="form-select me-2" onchange="this.form.submit()">
                                <option value="5" {{ request('limit') == 5 ? 'selected' : '' }}>5 posts</option>
                                <option value="10" {{ request('limit') == 10 || !request('limit') ? 'selected' : '' }}>10 posts</option>
                                <option value="20" {{ request('limit') == 20 ? 'selected' : '' }}>20 posts</option>
                                <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50 posts</option>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('wordpress.pages.index') }}" class="btn btn-outline-secondary">Ver Páginas</a>
                    </div>
                </div>

                @if($posts->count() > 0)
                    <div class="row">
                        @foreach($posts as $post)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card post-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="#" class="text-decoration-none">
                                                {{ $post->post_title }}
                                            </a>
                                        </h5>
                                        
                                        @if($post->post_excerpt)
                                            <p class="post-excerpt">{{ Str::limit($post->post_excerpt, 150) }}</p>
                                        @else
                                            <p class="post-excerpt">{{ Str::limit(strip_tags($post->post_content), 150) }}</p>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($post->post_date)->format('d/m/Y') }}
                                            </small>
                                            <span class="badge bg-success">{{ $post->post_type }}</span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="http://wordpress.local/?p={{ $post->ID }}" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            Ver no WordPress
                                        </a>
                                        <a href="http://wordpress.local/wp-admin/post.php?post={{ $post->ID }}&action=edit" 
                                           target="_blank" class="btn btn-sm btn-outline-info">
                                            Editar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <h4>Nenhum post encontrado</h4>
                        <p>Não há posts publicados no WordPress ou a conexão com o banco não está funcionando.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 