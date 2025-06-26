<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($siteTitle); ?> - Laravel WordPress</title>
    <meta name="description" content="<?php echo e($siteDescription); ?>">
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
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($viewType === 'page' && $homePage): ?>
        <!-- Página inicial baseada em uma página específica -->
        <div class="container main-content">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Conteúdo da página inicial -->
                    <article>
                        <header class="mb-4">
                            <h1 class="display-4">
                                <i class="fas fa-home me-2"></i><?php echo e($homePage->post_title); ?>

                            </h1>
                            
                            <?php if($homePage->post_excerpt): ?>
                                <div class="lead mb-4">
                                    <i class="fas fa-quote-left me-2"></i><?php echo e($homePage->post_excerpt); ?>

                                </div>
                            <?php endif; ?>
                        </header>

                        <div class="page-content">
                            <?php echo $homePage->post_content; ?>

                        </div>
                    </article>
                </div>

                <div class="col-lg-4">
                    <!-- Sidebar com posts recentes -->
                    <div class="sidebar">
                        <h4><i class="fas fa-newspaper me-2"></i>Posts Recentes</h4>
                        
                        <?php if($posts->count() > 0): ?>
                            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="card post-card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="http://wordpress.local/?p=<?php echo e($post->ID); ?>" 
                                               target="_blank" class="text-decoration-none">
                                                <?php echo e(Str::limit($post->post_title, 50)); ?>

                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo e(\Carbon\Carbon::parse($post->post_date)->format('d/m/Y')); ?>

                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            
                            <div class="text-center">
                                <a href="<?php echo e(route('wordpress.posts.index')); ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-list me-1"></i>Ver todos os posts
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Nenhum post encontrado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Página inicial baseada em posts -->
        <div class="hero-section">
            <div class="container text-center">
                <h1 class="site-title"><?php echo e($siteTitle); ?></h1>
                <?php if($siteDescription): ?>
                    <p class="site-description"><?php echo e($siteDescription); ?></p>
                <?php endif; ?>
                <div class="mt-4">
                    <a href="<?php echo e(route('wordpress.pages.index')); ?>" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-file-alt me-1"></i>Ver Páginas
                    </a>
                    <a href="<?php echo e(route('wordpress.posts.index')); ?>" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-newspaper me-1"></i>Ver Posts
                    </a>
                </div>
            </div>
        </div>

        <div class="container main-content">
            <h2 class="mb-4">
                <i class="fas fa-newspaper me-2"></i>Posts Recentes
            </h2>
            
            <?php if($posts->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card post-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="http://wordpress.local/?p=<?php echo e($post->ID); ?>" 
                                           target="_blank" class="text-decoration-none">
                                            <i class="fas fa-newspaper me-1"></i><?php echo e($post->post_title); ?>

                                        </a>
                                    </h5>
                                    
                                    <?php if($post->post_excerpt): ?>
                                        <p class="card-text text-muted">
                                            <?php echo e(Str::limit($post->post_excerpt, 150)); ?>

                                        </p>
                                    <?php else: ?>
                                        <p class="card-text text-muted">
                                            <?php echo e(Str::limit(strip_tags($post->post_content), 150)); ?>

                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo e(\Carbon\Carbon::parse($post->post_date)->format('d/m/Y')); ?>

                                        </small>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-newspaper me-1"></i>Post
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="http://wordpress.local/?p=<?php echo e($post->ID); ?>" 
                                       target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>Ler no WordPress
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <div class="text-center mt-4">
                    <a href="<?php echo e(route('wordpress.posts.index')); ?>" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-list me-1"></i>Ver todos os posts
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <h4><i class="fas fa-info-circle me-2"></i>Nenhum post encontrado</h4>
                    <p>Não há posts publicados no WordPress.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> <?php /**PATH /var/www/html/resources/views/home.blade.php ENDPATH**/ ?>