<?php $__env->startSection('title', 'Minha Conta - WordPress Laravel Integration'); ?>
<?php $__env->startSection('description', 'Gerenciar sua conta e pedidos'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Minha Conta</h1>
                
                <?php if($isLoggedIn): ?>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-user me-2"></i>
                                        Informações da Conta
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Nome:</strong> <?php echo e($currentUser->display_name ?: $currentUser->user_login); ?></p>
                                            <p><strong>Email:</strong> <?php echo e($currentUser->user_email); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Usuário:</strong> <?php echo e($currentUser->user_login); ?></p>
                                            <p><strong>Membro desde:</strong> <?php echo e(\Carbon\Carbon::parse($currentUser->user_registered)->format('d/m/Y')); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Meus Pedidos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        Para ver seus pedidos completos, acesse o WordPress:
                                    </p>
                                    <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/my-account" 
                                       target="_blank" class="btn btn-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        Acessar Minha Conta no WordPress
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-cog me-2"></i>
                                        Ações Rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/my-account/orders" 
                                           target="_blank" class="btn btn-outline-primary">
                                            <i class="fas fa-list me-1"></i>
                                            Ver Pedidos
                                        </a>
                                        <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/my-account/edit-account" 
                                           target="_blank" class="btn btn-outline-secondary">
                                            <i class="fas fa-edit me-1"></i>
                                            Editar Conta
                                        </a>
                                        <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/my-account/edit-address" 
                                           target="_blank" class="btn btn-outline-info">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            Endereços
                                        </a>
                                        <a href="<?php echo e(route('wordpress.logout')); ?>" 
                                           class="btn btn-outline-danger">
                                            <i class="fas fa-sign-out-alt me-1"></i>
                                            Sair
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <div class="alert alert-warning">
                            <h4><i class="fas fa-exclamation-triangle me-2"></i>Não logado</h4>
                            <p>Você precisa estar logado para acessar esta página.</p>
                            <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/wp-login.php" 
                               target="_blank" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-1"></i>
                                Fazer Login
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/wordpress/pages/my-account.blade.php ENDPATH**/ ?>