<?php $__env->startSection('title', 'Minha Conta - WordPress Laravel Integration'); ?>
<?php $__env->startSection('description', 'Gerenciar sua conta e pedidos'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('components.wordpress-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo e(session('error')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

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
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Fazer Login
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if($errors->any()): ?>
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo e(route('wordpress.login')); ?>">
                                        <?php echo csrf_field(); ?>
                                        
                                        <div class="mb-3">
                                            <label for="log" class="form-label">Usuário ou Email</label>
                                            <input type="text" 
                                                   class="form-control <?php $__errorArgs = ['log'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="log" 
                                                   name="log" 
                                                   value="<?php echo e(old('log')); ?>" 
                                                   required 
                                                   autocomplete="username">
                                            <?php $__errorArgs = ['log'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label for="pwd" class="form-label">Senha</label>
                                            <input type="password" 
                                                   class="form-control <?php $__errorArgs = ['pwd'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   id="pwd" 
                                                   name="pwd" 
                                                   required 
                                                   autocomplete="current-password">
                                            <?php $__errorArgs = ['pwd'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3 form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input" 
                                                   id="rememberme" 
                                                   name="rememberme" 
                                                   value="1">
                                            <label class="form-check-label" for="rememberme">
                                                Lembrar de mim
                                            </label>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-sign-in-alt me-1"></i>
                                                Entrar
                                            </button>
                                        </div>
                                    </form>

                                    <hr class="my-4">

                                    <div class="text-center">
                                        <p class="text-muted mb-2">Ou acesse diretamente no WordPress:</p>
                                        <a href="<?php echo e(\App\Models\WordPressSettings::getWordPressUrl()); ?>/wp-login.php" 
                                           target="_blank" 
                                           class="btn btn-outline-secondary">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            Login no WordPress
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/wordpress/pages/my-account.blade.php ENDPATH**/ ?>