<?php $__env->startSection('title', $page->post_title . ' - WordPress Laravel Integration'); ?>
<?php $__env->startSection('description', Str::limit(strip_tags($page->post_content), 160)); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container main-content">
        <div class="row">
            <div class="col-lg-8">
                <article>
                    <header class="mb-4">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="<?php echo e(route('wordpress.pages.index')); ?>">Páginas</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo e($page->post_title); ?></li>
                            </ol>
                        </nav>
                        
                        <h1 class="mb-3"><?php echo e($page->post_title); ?></h1>
                        
                        <div class="text-muted mb-3">
                            <small>
                                <i class="fas fa-calendar me-1"></i>
                                Publicado em <?php echo e(\Carbon\Carbon::parse($page->post_date)->format('d/m/Y \à\s H:i')); ?>

                            </small>
                            <?php if($page->post_modified !== $page->post_date): ?>
                                <br>
                                <small>
                                    <i class="fas fa-edit me-1"></i>
                                    Atualizado em <?php echo e(\Carbon\Carbon::parse($page->post_modified)->format('d/m/Y \à\s H:i')); ?>

                                </small>
                            <?php endif; ?>
                        </div>
                    </header>

                    <div class="page-content">
                        <?php echo $page->post_content; ?>

                    </div>
                </article>
            </div>
            
            <div class="col-lg-4">
                <div class="bg-light p-3 rounded">
                    <h5>Informações da Página</h5>
                    <ul class="list-unstyled">
                        <li><strong>ID:</strong> <?php echo e($page->ID); ?></li>
                        <li><strong>Slug:</strong> <?php echo e($page->post_name); ?></li>
                        <li><strong>Status:</strong> <?php echo e($page->post_status); ?></li>
                        <li><strong>Tipo:</strong> <?php echo e($page->post_type); ?></li>
                    </ul>
                    
                    <?php if($meta && is_array($meta) && count($meta) > 0): ?>
                        <h6 class="mt-3">Meta Dados</h6>
                        <ul class="list-unstyled">
                            <?php $__currentLoopData = $meta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><strong><?php echo e($key); ?>:</strong> <?php echo e(Str::limit($value, 50)); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/wordpress/pages/show.blade.php ENDPATH**/ ?>