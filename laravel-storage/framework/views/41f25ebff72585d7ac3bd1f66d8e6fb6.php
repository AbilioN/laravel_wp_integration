<?php $__env->startSection('title', $siteTitle); ?>
<?php $__env->startSection('description', $siteDescription); ?>

<?php $__env->startSection('content'); ?>
    <!-- WordPress Dynamic Navbar -->
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4"><?php echo e($siteTitle); ?></h1>
                
                <?php if($viewType === 'page' && $homePage): ?>
                    <!-- Página inicial baseada em uma página específica -->
                    <div class="page-content">
                        <?php echo $homePage->post_content; ?>

                    </div>
                <?php else: ?>
                    <!-- Página inicial baseada em posts -->
                    <?php if($posts->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/?p=<?php echo e($post->ID); ?>" 
                                                   target="_blank" class="text-decoration-none">
                                                    <?php echo e($post->post_title); ?>

                                                </a>
                                            </h5>
                                            <p class="card-text text-muted">
                                                <?php if($post->post_excerpt): ?>
                                                    <?php echo e(Str::limit($post->post_excerpt, 150)); ?>

                                                <?php else: ?>
                                                    <?php echo e(Str::limit(strip_tags($post->post_content), 150)); ?>

                                                <?php endif; ?>
                                            </p>
                                            <small class="text-muted">
                                                <?php echo e(\Carbon\Carbon::parse($post->post_date)->format('d/m/Y')); ?>

                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <h3>Bem-vindo ao <?php echo e($siteTitle); ?></h3>
                            <p><?php echo e($siteDescription); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/home.blade.php ENDPATH**/ ?>