// Register Service Worker for PWA Offline Support
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // Check if offline mode is enabled
        fetch('/api/offline-mode-enabled')
            .then(response => response.json())
            .then(data => {
                if (data.enabled) {
                    return navigator.serviceWorker.register('/sw.js');
                } else {
                    // Unregister service worker if offline mode is disabled
                    return navigator.serviceWorker.getRegistrations().then(registrations => {
                        for (let registration of registrations) {
                            registration.unregister();
                            console.log('Service Worker unregistered (offline mode disabled)');
                        }
                    });
                }
            })
            .then((registration) => {
                if (registration) {
                    console.log('Service Worker registered successfully:', registration.scope);
                }
                
                // Check for updates every time
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    if (newWorker) {
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // New service worker available
                                console.log('New service worker available');
                                showUpdateNotification();
                            }
                        });
                    }
                });
            })
            .catch((error) => {
                console.error('Service Worker registration failed:', error);
            });
        
        // Listen for messages from service worker
        navigator.serviceWorker.addEventListener('message', (event) => {
            console.log('Message from service worker:', event.data);
        });
    });
    
    // Handle controller changes (new service worker activated)
    let refreshing = false;
    navigator.serviceWorker.addEventListener('controllerchange', () => {
        if (refreshing) return;
        refreshing = true;
        console.log('New service worker activated, reloading...');
        window.location.reload();
    });
}

// Show update notification
function showUpdateNotification() {
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('New version available', {
            body: 'An update is ready. Click to reload.',
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: 'update-available'
        }).onclick = () => {
            window.location.reload();
        };
    }
}

// Request notification permission
function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                console.log('Notification permission granted');
            }
        });
    }
}

// Install prompt handler
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent the mini infobar from appearing
    e.preventDefault();
    deferredPrompt = e;
    
    // Show install button
    showInstallButton();
});

function showInstallButton() {
    const installBtn = document.getElementById('install-btn');
    if (installBtn) {
        installBtn.style.display = 'inline-flex';
        installBtn.addEventListener('click', installApp);
    }
}

async function installApp() {
    if (!deferredPrompt) {
        return;
    }
    
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
        console.log('User accepted the install prompt');
    } else {
        console.log('User dismissed the install prompt');
    }
    
    deferredPrompt = null;
    const installBtn = document.getElementById('install-btn');
    if (installBtn) {
        installBtn.style.display = 'none';
    }
}

// Listen for app installed event
window.addEventListener('appinstalled', () => {
    console.log('PWA was installed');
    deferredPrompt = null;
});

// Network status handling
window.addEventListener('online', () => {
    console.log('App is back online');
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Back Online!',
            text: 'Your connection has been restored.',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
});

window.addEventListener('offline', () => {
    console.log('App is offline');
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'You\'re Offline',
            text: 'Some features may not be available.',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
});

