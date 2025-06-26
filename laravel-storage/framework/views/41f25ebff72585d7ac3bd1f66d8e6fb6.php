<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($siteTitle); ?></title>
    <meta name="description" content="<?php echo e($siteDescription); ?>">
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
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container main-content">
        <?php if($viewType === 'page' && $homePage): ?>
            <!-- Página inicial baseada em uma página específica -->
            <div class="row">
                <div class="col-lg-8">
                    <!-- Conteúdo da página inicial -->
                    <article>
                        <header class="mb-4">
                            <h1><?php echo e($homePage->post_title); ?></h1>
                            
                            <?php if($homePage->post_excerpt): ?>
                                <div class="lead mb-4">
                                    <?php echo e($homePage->post_excerpt); ?>

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
                    <div class="bg-light p-3 rounded">
                        <h4>Blog</h4>
                        
                        <?php if($posts->count() > 0): ?>
                            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-3">
                                    <h6>
                                        <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/?p=<?php echo e($post->ID); ?>" 
                                           target="_blank" class="text-decoration-none">
                                            <?php echo e($post->post_title); ?>

                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo e(\Carbon\Carbon::parse($post->post_date)->format('F j, Y')); ?>

                                    </small>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <p class="text-muted">Nenhum post encontrado.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Página inicial baseada em posts -->
            <div class="row">
                <div class="col-12">
                    <?php if($posts->count() > 0): ?>
                        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <article class="post-card">
                                <h2 class="post-title">
                                    <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/?p=<?php echo e($post->ID); ?>" 
                                       target="_blank">
                                        <?php echo e($post->post_title); ?>

                                    </a>
                                </h2>
                                
                                <div class="post-date">
                                    <?php echo e(\Carbon\Carbon::parse($post->post_date)->format('F j, Y')); ?>

                                </div>
                                
                                <div class="post-excerpt">
                                    <?php if($post->post_excerpt): ?>
                                        <?php echo e($post->post_excerpt); ?>

                                    <?php else: ?>
                                        <?php echo e(Str::limit(strip_tags($post->post_content), 300)); ?>

                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="text-center">
                            <h3>Nenhum post encontrado</h3>
                            <p>Não há posts publicados no momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> <?php /**PATH /var/www/html/resources/views/home.blade.php ENDPATH**/ ?>