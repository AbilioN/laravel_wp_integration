<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'WordPress Laravel Integration'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', 'Integração WordPress com Laravel'); ?>">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#007bff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="WP Laravel">
    <meta name="msapplication-TileColor" content="#007bff">
    <meta name="msapplication-config" content="/browserconfig.xml">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <style>
        /* PWA Styles */
        .offline {
            position: relative;
        }
        
        .offline::before {
            content: 'Offline';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #dc3545;
            color: white;
            text-align: center;
            padding: 5px;
            z-index: 9999;
            font-size: 12px;
        }
        
        #install-pwa {
            margin: 0 10px;
        }
        
        /* Loading indicator */
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        
        .loading.show {
            display: flex;
        }
        
        /* Page specific styles */
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
        
        <?php echo $__env->yieldContent('styles'); ?>
    </style>
</head>
<body>
    <!-- Loading indicator -->
    <div class="loading" id="loading">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>

    <!-- Content -->
    <?php echo $__env->yieldContent('content'); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- PWA Scripts -->
    <script src="/js/pwa.js"></script>
    
    <script>
        // Show loading indicator on page load
        document.addEventListener('DOMContentLoaded', function() {
            const loading = document.getElementById('loading');
            if (loading) {
                loading.classList.add('show');
                setTimeout(() => {
                    loading.classList.remove('show');
                }, 1000);
            }
        });
        
        // Show loading indicator on navigation
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.href && !e.target.href.startsWith('javascript:')) {
                const loading = document.getElementById('loading');
                if (loading) {
                    loading.classList.add('show');
                }
            }
        });
        
        <?php echo $__env->yieldContent('scripts'); ?>
    </script>
</body>
</html> <?php /**PATH /var/www/html/resources/views/layouts/pwa.blade.php ENDPATH**/ ?>