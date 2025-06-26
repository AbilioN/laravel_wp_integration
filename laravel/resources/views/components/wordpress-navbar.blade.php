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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Logo/Brand -->
        <a class="navbar-brand" href="{{ route('wordpress.pages.index') }}">
            <i class="fas fa-home me-2"></i>
            Laravel WordPress
        </a>

        <!-- Toggle button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('wordpress.pages.index') ? 'active' : '' }}" 
                       href="{{ route('wordpress.pages.index') }}">
                        <i class="fas fa-home me-1"></i>Início
                    </a>
                </li>

                <!-- Dynamic WordPress Pages -->
                @foreach($navigationPages as $page)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wordpress.pages.show') && request()->route('slug') == $page->post_name ? 'active' : '' }}" 
                           href="{{ route('wordpress.pages.show', $page->post_name) }}">
                            {{ $page->post_title }}
                        </a>
                    </li>
                @endforeach

                <!-- Posts -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('wordpress.posts.index') ? 'active' : '' }}" 
                       href="{{ route('wordpress.posts.index') }}">
                        <i class="fas fa-newspaper me-1"></i>Posts
                    </a>
                </li>
            </ul>

            <!-- Search form -->
            <form class="d-flex me-3" action="{{ route('wordpress.pages.search') }}" method="GET">
                <input class="form-control me-2" type="search" name="q" placeholder="Buscar páginas..." 
                       value="{{ request('q') }}" aria-label="Search">
                <button class="btn btn-outline-light" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <!-- Recent posts dropdown -->
            @if($recentPosts->count() > 0)
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="recentPostsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-clock me-1"></i>Posts Recentes
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="recentPostsDropdown">
                            @foreach($recentPosts as $post)
                                <li>
                                    <a class="dropdown-item" href="http://wordpress.local/?p={{ $post->ID }}" target="_blank">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ Str::limit($post->post_title, 30) }}</span>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($post->post_date)->format('d/m/Y') }}</small>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('wordpress.posts.index') }}">
                                    <i class="fas fa-list me-1"></i>Ver todos os posts
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</nav>

<!-- Page count indicator -->
<div class="bg-light py-2 border-bottom">
    <div class="container">
        <small class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            {{ $navigationPages->count() }} páginas disponíveis
            @if($recentPosts->count() > 0)
                • {{ $recentPosts->count() }} posts recentes
            @endif
            <span class="ms-2">
                <i class="fas fa-sync-alt me-1"></i>
                <small>Atualizado automaticamente</small>
            </span>
        </small>
    </div>
</div> 