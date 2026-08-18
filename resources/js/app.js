import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const sanctumToken = document.querySelector('meta[name="sanctum-token"]')?.content;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

try {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
        wsPort: parseInt(import.meta.env.VITE_REVERB_PORT ?? '8080'),
        wssPort: parseInt(import.meta.env.VITE_REVERB_PORT ?? '443'),
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: sanctumToken ? '/api/broadcasting/auth' : '/broadcasting/auth',
        auth: {
            headers: {
                ...(sanctumToken ? { Authorization: `Bearer ${sanctumToken}` } : {}),
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                Accept: 'application/json',
            },
        },
    });
} catch (e) {
    console.warn('Echo initialization failed:', e);
}

window.Alpine = Alpine;

Alpine.start();
