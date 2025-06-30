@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Produtos</h1>
        <div class="flex space-x-4">
            <a href="{{ route('cart.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                </svg>
                <span>Carrinho</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nome do produto...">
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                <select name="category" id="category" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todas as categorias</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->term_id }}" {{ request('category') == $category->term_id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Ordenar</label>
                <select name="sort" id="sort" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="date" {{ request('sort') == 'date' ? 'selected' : '' }}>Mais recentes</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Nome A-Z</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Menor preço</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Maior preço</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de Produtos -->
    @if($products && $products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Imagem do Produto -->
                    <div class="relative h-48 bg-gray-200">
                        @if($product->featured_image)
                            <img src="{{ $product->featured_image }}" 
                                 alt="{{ $product->post_title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Badge de Status -->
                        @if($product->post_status === 'publish')
                            <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs">
                                Disponível
                            </div>
                        @else
                            <div class="absolute top-2 left-2 bg-gray-500 text-white px-2 py-1 rounded-full text-xs">
                                Indisponível
                            </div>
                        @endif
                    </div>

                    <!-- Informações do Produto -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            {{ $product->post_title }}
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            {{ Str::limit(strip_tags($product->post_content), 100) }}
                        </p>

                        <!-- Preço -->
                        <div class="flex items-center justify-between mb-4">
                            @if(isset($product->meta->_regular_price) && $product->meta->_regular_price)
                                <div class="flex items-center space-x-2">
                                    @if(isset($product->meta->_sale_price) && $product->meta->_sale_price && $product->meta->_sale_price < $product->meta->_regular_price)
                                        <span class="text-lg font-bold text-green-600">
                                            R$ {{ number_format($product->meta->_sale_price, 2, ',', '.') }}
                                        </span>
                                        <span class="text-sm text-gray-500 line-through">
                                            R$ {{ number_format($product->meta->_regular_price, 2, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-lg font-bold text-gray-900">
                                            R$ {{ number_format($product->meta->_regular_price, 2, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-lg font-bold text-gray-900">Preço sob consulta</span>
                            @endif
                        </div>

                        <!-- Botões de Ação -->
                        <div class="flex space-x-2">
                            <a href="{{ route('products.show', $product->ID) }}" 
                               class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-2 rounded-md text-center text-sm transition-colors duration-200">
                                Ver Detalhes
                            </a>
                            
                            @if($product->post_status === 'publish')
                                <form method="POST" action="{{ route('cart.add') }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->ID }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" 
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm transition-colors duration-200">
                                        Adicionar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        @if($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @endif

    @else
        <!-- Estado Vazio -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto encontrado</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request('search') || request('category'))
                    Tente ajustar os filtros de busca.
                @else
                    Não há produtos disponíveis no momento.
                @endif
            </p>
        </div>
    @endif
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection 