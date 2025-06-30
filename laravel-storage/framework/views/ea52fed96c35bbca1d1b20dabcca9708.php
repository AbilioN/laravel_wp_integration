<?php $__env->startSection('title', 'Posts - WordPress Laravel Integration'); ?>
<?php $__env->startSection('description', 'Lista de todos os posts do WordPress'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Posts</h1>
                
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
                        <h3>Nenhum post encontrado</h3>
                        <p>Não há posts publicados no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/wordpress/posts/index.blade.php ENDPATH**/ ?>