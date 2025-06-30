@extends('layouts.app')

@section('title', 'Carrinho de Compras')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Carrinho de Compras</h1>
        <div class="flex space-x-4">
            <a href="{{ route('products.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span>Continuar Comprando</span>
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

    @if($cartItems && count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Lista de Itens -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">Itens no Carrinho</h2>
                    </div>
                    
                    <div class="divide-y">
                        @foreach($cartItems as $item)
                            <div class="p-6 flex items-center space-x-4">
                                <!-- Imagem do Produto -->
                                <div class="flex-shrink-0">
                                    @if(isset($item['image']) && $item['image'])
                                        <img src="{{ $item['image'] }}" 
                                             alt="{{ $item['name'] }}" 
                                             class="w-20 h-20 object-cover rounded-lg">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Informações do Produto -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-medium text-gray-900 truncate">
                                        {{ $item['name'] ?? 'Produto sem nome' }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        ID: {{ $item['id'] ?? 'N/A' }}
                                    </p>
                                </div>

                                <!-- Quantidade -->
                                <div class="flex items-center space-x-2">
                                    <form method="POST" action="{{ route('cart.update') }}" class="flex items-center space-x-2">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                        <button type="submit" name="action" value="decrease" 
                                                class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <span class="w-12 text-center font-medium">{{ $item['quantity'] ?? 1 }}</span>
                                        <button type="submit" name="action" value="increase" 
                                                class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <!-- Preço -->
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900">
                                        R$ {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2, ',', '.') }}
                                    </p>
                                    @if(($item['quantity'] ?? 1) > 1)
                                        <p class="text-sm text-gray-500">
                                            R$ {{ number_format($item['price'] ?? 0, 2, ',', '.') }} cada
                                        </p>
                                    @endif
                                </div>

                                <!-- Remover -->
                                <div class="flex-shrink-0">
                                    <form method="POST" action="{{ route('cart.remove', $item['id']) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-2"
                                                onclick="return confirm('Tem certeza que deseja remover este item?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Resumo do Pedido -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ $totalItems }} itens):</span>
                            <span class="font-medium">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Frete:</span>
                            <span class="font-medium">Calculado no checkout</span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total:</span>
                            <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('cart.checkout') }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium text-center block">
                            Finalizar Compra
                        </a>
                        
                        <form method="POST" action="{{ route('cart.clear') }}" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg font-medium"
                                    onclick="return confirm('Tem certeza que deseja limpar o carrinho?')">
                                Limpar Carrinho
                            </button>
                        </form>
                    </div>

                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Políticas de Compra</h3>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Frete calculado no checkout</li>
                            <li>• Entrega em até 5 dias úteis</li>
                            <li>• Devolução em até 30 dias</li>
                            <li>• Pagamento seguro</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Carrinho Vazio -->
        <div class="text-center py-12">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Seu carrinho está vazio</h3>
            <p class="mt-2 text-gray-500">Adicione alguns produtos para começar suas compras.</p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                    Ver Produtos
                </a>
            </div>
        </div>
    @endif
</div>
@endsection 