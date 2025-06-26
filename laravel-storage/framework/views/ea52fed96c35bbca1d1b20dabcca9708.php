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
                                <option value="5" <?php echo e(request('limit') == 5 ? 'selected' : ''); ?>>5 posts</option>
                                <option value="10" <?php echo e(request('limit') == 10 || !request('limit') ? 'selected' : ''); ?>>10 posts</option>
                                <option value="20" <?php echo e(request('limit') == 20 ? 'selected' : ''); ?>>20 posts</option>
                                <option value="50" <?php echo e(request('limit') == 50 ? 'selected' : ''); ?>>50 posts</option>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?php echo e(route('wordpress.pages.index')); ?>" class="btn btn-outline-secondary">Ver Páginas</a>
                    </div>
                </div>

                <?php if($posts->count() > 0): ?>
                    <div class="row">
                        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card post-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="#" class="text-decoration-none">
                                                <?php echo e($post->post_title); ?>

                                            </a>
                                        </h5>
                                        
                                        <?php if($post->post_excerpt): ?>
                                            <p class="post-excerpt"><?php echo e(Str::limit($post->post_excerpt, 150)); ?></p>
                                        <?php else: ?>
                                            <p class="post-excerpt"><?php echo e(Str::limit(strip_tags($post->post_content), 150)); ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <?php echo e(\Carbon\Carbon::parse($post->post_date)->format('d/m/Y')); ?>

                                            </small>
                                            <span class="badge bg-success"><?php echo e($post->post_type); ?></span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="http://wordpress.local/?p=<?php echo e($post->ID); ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            Ver no WordPress
                                        </a>
                                        <a href="http://wordpress.local/wp-admin/post.php?post=<?php echo e($post->ID); ?>&action=edit" 
                                           target="_blank" class="btn btn-sm btn-outline-info">
                                            Editar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4>Nenhum post encontrado</h4>
                        <p>Não há posts publicados no WordPress ou a conexão com o banco não está funcionando.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> <?php /**PATH /var/www/html/resources/views/wordpress/posts/index.blade.php ENDPATH**/ ?>