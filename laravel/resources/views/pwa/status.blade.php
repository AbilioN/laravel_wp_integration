@extends('layouts.pwa')

@section('title', 'PWA Status - ' . $pwaInfo['name'])
@section('description', 'Status e informações da Progressive Web App')

@section('styles')
<style>
    .feature-card {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: transform 0.2s;
    }
    
    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    
    .status-online { background-color: #28a745; }
    .status-offline { background-color: #dc3545; }
    .status-warning { background-color: #ffc107; }
</style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-mobile-alt text-primary"></i>
                    PWA Status
                </h1>
                
                <!-- PWA Info Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i>
                            Informações da PWA
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nome:</strong> {{ $pwaInfo['name'] }}</p>
                                <p><strong>Nome Curto:</strong> {{ $pwaInfo['short_name'] }}</p>
                                <p><strong>Versão:</strong> {{ $pwaInfo['version'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Manifest:</strong> <a href="{{ $pwaInfo['manifest_url'] }}" target="_blank">{{ $pwaInfo['manifest_url'] }}</a></p>
                                <p><strong>Service Worker:</strong> <a href="{{ $pwaInfo['sw_url'] }}" target="_blank">{{ $pwaInfo['sw_url'] }}</a></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Indicators -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="feature-card text-center">
                            <i class="fas fa-wifi fa-2x text-primary mb-2"></i>
                            <h6>Conectividade</h6>
                            <span class="status-indicator status-online" id="connectivity-status"></span>
                            <span id="connectivity-text">Online</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-card text-center">
                            <i class="fas fa-download fa-2x text-success mb-2"></i>
                            <h6>Instalável</h6>
                            <span class="status-indicator status-online" id="installable-status"></span>
                            <span id="installable-text">Disponível</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-card text-center">
                            <i class="fas fa-bell fa-2x text-warning mb-2"></i>
                            <h6>Notificações</h6>
                            <span class="status-indicator status-warning" id="notification-status"></span>
                            <span id="notification-text">Solicitar</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-card text-center">
                            <i class="fas fa-sync fa-2x text-info mb-2"></i>
                            <h6>Background Sync</h6>
                            <span class="status-indicator status-online" id="sync-status"></span>
                            <span id="sync-text">Disponível</span>
                        </div>
                    </div>
                </div>
                
                <!-- Features -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star"></i>
                            Funcionalidades
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($pwaInfo['features'] as $feature)
                                <div class="col-md-6">
                                    <div class="feature-card">
                                        <i class="fas fa-check-circle text-success"></i>
                                        {{ $feature }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs"></i>
                            Ações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-primary btn-sm w-100" id="install-btn" style="display: none;">
                                    <i class="fas fa-download"></i> Instalar PWA
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-warning btn-sm w-100" id="notification-btn">
                                    <i class="fas fa-bell"></i> Ativar Notificações
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-info btn-sm w-100" id="update-btn">
                                    <i class="fas fa-sync"></i> Verificar Atualizações
                                </button>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button class="btn btn-secondary btn-sm w-100" id="clear-cache-btn">
                                    <i class="fas fa-trash"></i> Limpar Cache
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check connectivity
        function updateConnectivity() {
            const status = document.getElementById('connectivity-status');
            const text = document.getElementById('connectivity-text');
            
            if (navigator.onLine) {
                status.className = 'status-indicator status-online';
                text.textContent = 'Online';
            } else {
                status.className = 'status-indicator status-offline';
                text.textContent = 'Offline';
            }
        }
        
        window.addEventListener('online', updateConnectivity);
        window.addEventListener('offline', updateConnectivity);
        updateConnectivity();
        
        // Check installable
        function updateInstallable() {
            const btn = document.getElementById('install-btn');
            if (window.pwaManager && window.pwaManager.swRegistration) {
                btn.style.display = 'block';
            }
        }
        
        // Notification button
        document.getElementById('notification-btn').addEventListener('click', async function() {
            if ('Notification' in window) {
                const permission = await Notification.requestPermission();
                const status = document.getElementById('notification-status');
                const text = document.getElementById('notification-text');
                
                if (permission === 'granted') {
                    status.className = 'status-indicator status-online';
                    text.textContent = 'Ativado';
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-check"></i> Ativado';
                } else {
                    status.className = 'status-indicator status-offline';
                    text.textContent = 'Negado';
                }
            }
        });
        
        // Update button
        document.getElementById('update-btn').addEventListener('click', function() {
            if (window.pwaManager) {
                window.pwaManager.checkForAppUpdate();
                alert('Verificação de atualizações iniciada!');
            }
        });
        
        // Clear cache button
        document.getElementById('clear-cache-btn').addEventListener('click', async function() {
            try {
                const response = await fetch('/pwa/clear-cache', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                alert(result.message);
            } catch (error) {
                alert('Erro ao limpar cache: ' + error.message);
            }
        });
        
        // Initialize
        setTimeout(updateInstallable, 1000);
    });
</script>
@endsection 