// PWA Management Script
class PWAManager {
    constructor() {
        this.swRegistration = null;
        this.deferredPrompt = null;
        this.init();
    }

    async init() {
        if ('serviceWorker' in navigator) {
            try {
                this.swRegistration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker registered successfully:', this.swRegistration);
                this.setupEventListeners();
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        } else {
            console.log('Service Worker not supported');
        }
    }

    setupEventListeners() {
        // Listen for service worker updates
        if (this.swRegistration) {
            this.swRegistration.addEventListener('updatefound', () => {
                const newWorker = this.swRegistration.installing;
                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        this.showUpdateNotification();
                    }
                });
            });
        }

        // Listen for messages from service worker
        navigator.serviceWorker.addEventListener('message', (event) => {
            console.log('Message from Service Worker:', event.data);
        });

        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('beforeinstallprompt event fired');
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
        });

        // Listen for appinstalled event
        window.addEventListener('appinstalled', (evt) => {
            console.log('PWA was installed');
            this.deferredPrompt = null;
            this.hideInstallButton();
        });
    }

    showInstallButton() {
        console.log('Showing install button');
        const installButton = document.getElementById('install-pwa');
        if (installButton) {
            installButton.style.display = 'block';
            installButton.addEventListener('click', () => {
                this.installPWA();
            });
        }
        
        // Also show a notification
        this.showNotification('PWA Disponível', 'Clique no botão "Instalar App" para instalar esta aplicação!');
    }

    hideInstallButton() {
        const installButton = document.getElementById('install-pwa');
        if (installButton) {
            installButton.style.display = 'none';
        }
    }

    async installPWA() {
        if (this.deferredPrompt) {
            console.log('Prompting for install');
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            console.log(`User response to the install prompt: ${outcome}`);
            this.deferredPrompt = null;
            this.hideInstallButton();
        } else {
            console.log('No deferred prompt available');
        }
    }

    showUpdateNotification() {
        if (confirm('Uma nova versão está disponível! Deseja atualizar?')) {
            window.location.reload();
        }
    }

    async requestNotificationPermission() {
        if ('Notification' in window) {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                console.log('Notification permission granted');
                return true;
            }
        }
        return false;
    }

    async subscribeToPushNotifications() {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            try {
                const subscription = await this.swRegistration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY')
                });
                console.log('Push notification subscription:', subscription);
                return subscription;
            } catch (error) {
                console.error('Failed to subscribe to push notifications:', error);
                return null;
            }
        }
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    async checkForAppUpdate() {
        if (this.swRegistration) {
            await this.swRegistration.update();
        }
    }

    // Offline/Online status
    setupConnectivityListener() {
        window.addEventListener('online', () => {
            document.body.classList.remove('offline');
            this.showNotification('Conexão restaurada', 'Você está online novamente');
        });

        window.addEventListener('offline', () => {
            document.body.classList.add('offline');
            this.showNotification('Sem conexão', 'Você está offline. Algumas funcionalidades podem não estar disponíveis.');
        });
    }

    showNotification(title, body) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body: body,
                icon: '/icons/icon-192x192.png'
            });
        } else {
            // Fallback to alert if notifications not available
            alert(`${title}: ${body}`);
        }
    }

    // Background sync
    async registerBackgroundSync() {
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            try {
                await this.swRegistration.sync.register('background-sync');
                console.log('Background sync registered');
            } catch (error) {
                console.error('Background sync registration failed:', error);
            }
        }
    }

    // Check if PWA is installable
    checkInstallability() {
        console.log('Checking PWA installability...');
        
        // Check if manifest is loaded
        const manifestLink = document.querySelector('link[rel="manifest"]');
        if (!manifestLink) {
            console.error('Manifest not found');
            return false;
        }
        
        // Check if service worker is registered
        if (!this.swRegistration) {
            console.error('Service Worker not registered');
            return false;
        }
        
        // Check if running in HTTPS or localhost
        if (!window.location.protocol.includes('https') && !window.location.hostname.includes('localhost')) {
            console.error('PWA requires HTTPS (except localhost)');
            return false;
        }
        
        console.log('PWA is installable!');
        return true;
    }
}

// Initialize PWA when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.pwaManager = new PWAManager();
    
    // Setup connectivity listener
    window.pwaManager.setupConnectivityListener();
    
    // Add PWA install button to navbar if it doesn't exist
    const navbar = document.querySelector('.navbar-nav');
    if (navbar && !document.getElementById('install-pwa')) {
        const installButton = document.createElement('li');
        installButton.className = 'nav-item';
        installButton.innerHTML = `
            <button id="install-pwa" class="btn btn-outline-primary btn-sm" style="display: none;">
                <i class="fas fa-download"></i> Instalar App
            </button>
        `;
        navbar.appendChild(installButton);
    }
    
    // Check installability after a short delay
    setTimeout(() => {
        window.pwaManager.checkInstallability();
    }, 2000);
});

// Add offline indicator styles
const style = document.createElement('style');
style.textContent = `
    .offline {
        position: relative;
    }
    
    .offline::before {
        content: 'Offline';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: #dc3545;
        color: white;
        text-align: center;
        padding: 5px;
        z-index: 9999;
        font-size: 12px;
    }
    
    #install-pwa {
        margin: 0 10px;
    }
`;
document.head.appendChild(style); 