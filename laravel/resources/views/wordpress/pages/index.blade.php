@extends('layouts.pwa')

@section('title', 'Páginas - WordPress Laravel Integration')
@section('description', 'Lista de todas as páginas do WordPress')

@section('content')
    @include('components.wordpress-navbar')

    <div class="container main-content">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Páginas</h1>
                
                @if($pages->count() > 0)
                    <div class="row">
                        @foreach($pages as $page)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="{{ route('wordpress.pages.show', $page->post_name) }}" 
                                               class="text-decoration-none">
                                                {{ $page->post_title }}
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted">
                                            {{ Str::limit(strip_tags($page->post_content), 150) }}
                                        </p>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($page->post_date)->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center">
                        <h3>Nenhuma página encontrada</h3>
                        <p>Não há páginas publicadas no momento.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 