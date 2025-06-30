@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    {{ substr($user->display_name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->display_name }}</h1>
                    <p class="text-gray-600">{{ $user->email }}</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Informações do Usuário -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações do Usuário</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome de Usuário</label>
                            <p class="text-gray-900">{{ $user->username }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome de Exibição</label>
                            <p class="text-gray-900">{{ $user->display_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID do Usuário</label>
                            <p class="text-gray-900">{{ $user->id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Links Rápidos -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Links Rápidos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900">Dashboard</h3>
                                <p class="text-sm text-gray-600">Acessar painel principal</p>
                            </div>
                        </a>

                        <a href="{{ route('products.index') }}" 
                           class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900">Produtos</h3>
                                <p class="text-sm text-gray-600">Ver todos os produtos</p>
                            </div>
                        </a>

                        <a href="{{ route('cart.index') }}" 
                           class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900">Carrinho</h3>
                                <p class="text-sm text-gray-600">Ver carrinho de compras</p>
                            </div>
                        </a>

                        <a href="{{ route('my-account') }}" 
                           class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-gray-900">Minha Conta WordPress</h3>
                                <p class="text-sm text-gray-600">Acessar conta no WordPress</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Ações -->
                <div class="flex space-x-4 pt-6 border-t">
                    <form method="POST" action="{{ route('auth.logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors duration-200">
                            Sair da Conta
                        </button>
                    </form>
                    
                    <a href="{{ route('home') }}" 
                       class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium text-center transition-colors duration-200">
                        Voltar ao Início
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 