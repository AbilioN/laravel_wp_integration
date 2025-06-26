<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Páginas do WordPress - Laravel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .page-card {
            transition: transform 0.2s;
        }
        .page-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .page-excerpt {
            color: #666;
            font-size: 0.9em;
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
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-file-alt me-2"></i>
                    Páginas do WordPress
                </h1>
                
                <!-- Barra de pesquisa -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <form action="<?php echo e(route('wordpress.pages.search')); ?>" method="GET" class="d-flex">
                            <input type="text" name="q" class="form-control me-2" placeholder="Buscar páginas..." value="<?php echo e(request('q')); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Buscar
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?php echo e(route('wordpress.posts.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-newspaper me-1"></i>Ver Posts
                        </a>
                    </div>
                </div>

                <?php if($pages->count() > 0): ?>
                    <div class="row">
                        <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card page-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="<?php echo e(route('wordpress.pages.show', $page->post_name)); ?>" 
                                               class="page-title">
                                                <i class="fas fa-file-alt me-1"></i>
                                                <?php echo e($page->post_title); ?>

                                            </a>
                                        </h5>
                                        
                                        <?php if($page->post_excerpt): ?>
                                            <p class="page-excerpt"><?php echo e(Str::limit($page->post_excerpt, 150)); ?></p>
                                        <?php else: ?>
                                            <p class="page-excerpt"><?php echo e(Str::limit(strip_tags($page->post_content), 150)); ?></p>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo e(\Carbon\Carbon::parse($page->post_date)->format('d/m/Y')); ?>

                                            </small>
                                            <span class="badge bg-primary">
                                                <i class="fas fa-file me-1"></i><?php echo e($page->post_type); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="<?php echo e(route('wordpress.pages.show', $page->post_name)); ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>Ler página
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4><i class="fas fa-info-circle me-2"></i>Nenhuma página encontrada</h4>
                        <p>Não há páginas publicadas no WordPress ou a conexão com o banco não está funcionando.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> <?php /**PATH /var/www/html/resources/views/wordpress/pages/index.blade.php ENDPATH**/ ?>