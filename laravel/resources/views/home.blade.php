@extends('layouts.pwa')

@section('title', $siteTitle)
@section('description', $siteDescription)

@section('content')
    <!-- WordPress Dynamic Navbar -->
    @include('components.wordpress-navbar')

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">{{ $siteTitle }}</h1>
                
                @if($viewType === 'page' && $homePage)
                    <!-- Página inicial baseada em uma página específica -->
                    <div class="page-content">
                        {!! $homePage->post_content !!}
                    </div>
                @else
                    <!-- Página inicial baseada em posts -->
                    @if($posts->count() > 0)
                        <div class="row">
                            @foreach($posts as $post)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <a href="{{ \App\Models\WordPressSettings::getWordPressUrl() }}/?p={{ $post->ID }}" 
                                                   target="_blank" class="text-decoration-none">
                                                    {{ $post->post_title }}
                                                </a>
                                            </h5>
                                            <p class="card-text text-muted">
                                                @if($post->post_excerpt)
                                                    {{ Str::limit($post->post_excerpt, 150) }}
                                                @else
                                                    {{ Str::limit(strip_tags($post->post_content), 150) }}
                                                @endif
                                            </p>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($post->post_date)->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">
                            <h3>Bem-vindo ao {{ $siteTitle }}</h3>
                            <p>{{ $siteDescription }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection 