<?php
use App\Models\WordPressMenu;
use Illuminate\Support\Facades\Cache;

$navbarData = Cache::remember('wordpress_navbar', 300, function () {
    return [
        'pages' => WordPressMenu::getNavigationPages(),
        'recentPosts' => WordPressMenu::getRecentPosts(3)
    ];
});

$navigationPages = $navbarData['pages'];
$recentPosts = $navbarData['recentPosts'];
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <!-- Logo/Brand -->
        <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
            <?php echo e(\App\Models\WordPressSettings::getSiteTitle()); ?>

        </a>

        <!-- Toggle button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Dynamic WordPress Pages -->
                <?php $__currentLoopData = $navigationPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('wordpress.pages.show') && request()->route('slug') == $page->post_name ? 'active' : ''); ?>" 
                           href="<?php echo e(route('wordpress.pages.show', $page->post_name)); ?>">
                            <?php echo e($page->post_title); ?>

                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
</nav> <?php /**PATH /var/www/html/resources/views/components/wordpress-navbar.blade.php ENDPATH**/ ?>