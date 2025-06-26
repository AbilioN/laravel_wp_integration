<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page->post_title); ?> - <?php echo e(\App\Models\WordPressSettings::getSiteTitle()); ?></title>
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
                <!-- Conteúdo da página -->
                <article>
                    <header class="mb-4">
                        <h1><?php echo e($page->post_title); ?></h1>
                        
                        <?php if($page->post_excerpt): ?>
                            <div class="lead mb-4">
                                <?php echo e($page->post_excerpt); ?>

                            </div>
                        <?php endif; ?>
                    </header>

                    <div class="page-content">
                        <?php echo $page->post_content; ?>

                    </div>
                </article>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> <?php /**PATH /var/www/html/resources/views/wordpress/pages/show.blade.php ENDPATH**/ ?>