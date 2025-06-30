<?php $__env->startSection('title', 'Páginas - WordPress Laravel Integration'); ?>
<?php $__env->startSection('description', 'Lista de todas as páginas do WordPress'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Páginas</h1>
                
                <?php if($pages->count() > 0): ?>
                    <div class="row">
                        <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="<?php echo e(route('wordpress.pages.show', $page->post_name)); ?>" 
                                               class="text-decoration-none">
                                                <?php echo e($page->post_title); ?>

                                            </a>
                                        </h5>
                                        <p class="card-text text-muted">
                                            <?php echo e(Str::limit(strip_tags($page->post_content), 150)); ?>

                                        </p>
                                        <small class="text-muted">
                                            <?php echo e(\Carbon\Carbon::parse($page->post_date)->format('d/m/Y')); ?>

                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <h3>Nenhuma página encontrada</h3>
                        <p>Não há páginas publicadas no momento.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/wordpress/pages/index.blade.php ENDPATH**/ ?>