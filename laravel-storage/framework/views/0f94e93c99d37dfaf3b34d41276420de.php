<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page->post_title); ?> - Laravel</title>
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
        .page-meta {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
        }
        .meta-item {
            margin-bottom: 0.5rem;
        }
        .meta-label {
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Navegação -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?php echo e(route('wordpress.pages.index')); ?>">Páginas</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo e($page->post_title); ?></li>
                    </ol>
                </nav>

                <div class="row">
                    <div class="col-lg-8">
                        <!-- Conteúdo da página -->
                        <article>
                            <header class="mb-4">
                                <h1 class="display-5"><?php echo e($page->post_title); ?></h1>
                                <div class="text-muted mb-3">
                                    <small>
                                        <strong>Criado em:</strong> <?php echo e(\Carbon\Carbon::parse($page->post_date)->format('d/m/Y H:i')); ?>

                                        <?php if($page->post_modified != $page->post_date): ?>
                                            | <strong>Atualizado em:</strong> <?php echo e(\Carbon\Carbon::parse($page->post_modified)->format('d/m/Y H:i')); ?>

                                        <?php endif; ?>
                                    </small>
                                </div>
                                
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

                    <div class="col-lg-4">
                        <!-- Sidebar com informações -->
                        <div class="page-meta">
                            <h5>Informações da Página</h5>
                            
                            <div class="meta-item">
                                <span class="meta-label">ID:</span> <?php echo e($page->ID); ?>

                            </div>
                            
                            <div class="meta-item">
                                <span class="meta-label">Slug:</span> <?php echo e($page->post_name); ?>

                            </div>
                            
                            <div class="meta-item">
                                <span class="meta-label">Status:</span> 
                                <span class="badge bg-success"><?php echo e($page->post_status); ?></span>
                            </div>
                            
                            <div class="meta-item">
                                <span class="meta-label">Tipo:</span> 
                                <span class="badge bg-primary"><?php echo e($page->post_type); ?></span>
                            </div>

                            <?php if(!empty($meta)): ?>
                                <hr>
                                <h6>Meta Dados</h6>
                                <?php $__currentLoopData = $meta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!in_array($key, ['_edit_lock', '_edit_last'])): ?>
                                        <div class="meta-item">
                                            <span class="meta-label"><?php echo e($key); ?>:</span>
                                            <div class="small text-break"><?php echo e(Str::limit($value, 100)); ?></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>

                        <!-- Links úteis -->
                        <div class="mt-4">
                            <h5>Ações</h5>
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('wordpress.pages.index')); ?>" class="btn btn-outline-primary">
                                    ← Voltar para Páginas
                                </a>
                                <a href="<?php echo e(route('wordpress.posts.index')); ?>" class="btn btn-outline-secondary">
                                    Ver Posts
                                </a>
                                <a href="http://wordpress.local/wp-admin/post.php?post=<?php echo e($page->ID); ?>&action=edit" 
                                   target="_blank" class="btn btn-outline-info">
                                    Editar no WordPress
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> <?php /**PATH /var/www/html/resources/views/wordpress/pages/show.blade.php ENDPATH**/ ?>