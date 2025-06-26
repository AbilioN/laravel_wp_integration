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

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <!-- Logo/Brand -->
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ \App\Models\WordPressSettings::getSiteTitle() }}
        </a>

        <!-- Toggle button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Dynamic WordPress Pages -->
                @foreach($navigationPages as $page)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wordpress.pages.show') && request()->route('slug') == $page->post_name ? 'active' : '' }}" 
                           href="{{ route('wordpress.pages.show', $page->post_name) }}">
                            {{ $page->post_title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav> 