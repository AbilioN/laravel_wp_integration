@extends('layouts.pwa')

@section('title', $page->post_title . ' - WordPress Laravel Integration')
@section('description', Str::limit(strip_tags($page->post_content), 160))

@section('content')
    @include('components.wordpress-navbar')

    <div class="container main-content">
        <div class="row">
            <div class="col-lg-8">
                <article>
                    <header class="mb-4">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('wordpress.pages.index') }}">Páginas</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $page->post_title }}</li>
                            </ol>
                        </nav>
                        
                        <h1 class="mb-3">{{ $page->post_title }}</h1>
                        
                        <div class="text-muted mb-3">
                            <small>
                                <i class="fas fa-calendar me-1"></i>
                                Publicado em {{ \Carbon\Carbon::parse($page->post_date)->format('d/m/Y \à\s H:i') }}
                            </small>
                            @if($page->post_modified !== $page->post_date)
                                <br>
                                <small>
                                    <i class="fas fa-edit me-1"></i>
                                    Atualizado em {{ \Carbon\Carbon::parse($page->post_modified)->format('d/m/Y \à\s H:i') }}
                                </small>
                            @endif
                        </div>
                    </header>

                    <div class="page-content">
                        {!! $page->post_content !!}
                    </div>
                </article>
            </div>
            
            <div class="col-lg-4">
                <div class="bg-light p-3 rounded">
                    <h5>Informações da Página</h5>
                    <ul class="list-unstyled">
                        <li><strong>ID:</strong> {{ $page->ID }}</li>
                        <li><strong>Slug:</strong> {{ $page->post_name }}</li>
                        <li><strong>Status:</strong> {{ $page->post_status }}</li>
                        <li><strong>Tipo:</strong> {{ $page->post_type }}</li>
                    </ul>
                    
                    @if($meta && is_array($meta) && count($meta) > 0)
                        <h6 class="mt-3">Meta Dados</h6>
                        <ul class="list-unstyled">
                            @foreach($meta as $key => $value)
                                <li><strong>{{ $key }}:</strong> {{ Str::limit($value, 50) }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 