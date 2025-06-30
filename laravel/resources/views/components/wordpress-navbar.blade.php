@php
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
@endphp

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/">
            <i class="fas fa-home me-2"></i>
            {{ \App\Models\WordPressSettings::getSiteTitle() }}
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">
                        <i class="fas fa-home me-1"></i>Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('wordpress.pages.index') }}">
                        <i class="fas fa-file-alt me-1"></i>Páginas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('wordpress.posts.index') }}">
                        <i class="fas fa-newspaper me-1"></i>Posts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('wordpress.pages.my-account') }}">
                        <i class="fas fa-user me-1"></i>Minha Conta
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pwa.status') }}">
                        <i class="fas fa-mobile-alt me-1"></i>PWA Status
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <!-- PWA Install Button -->
                <li class="nav-item">
                    <button id="install-pwa" class="btn btn-outline-light btn-sm" style="display: none;">
                        <i class="fas fa-download me-1"></i>Instalar App
                    </button>
                </li>
                
                <!-- WordPress Link -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>WordPress
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav> 