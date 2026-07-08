import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const sanctumToken = document.querySelector('meta[name="sanctum-token"]')?.content;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: parseInt(import.meta.env.VITE_REVERB_PORT ?? '8080'),
    wssPort: parseInt(import.meta.env.VITE_REVERB_WSS_PORT ?? '443'),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    ...(sanctumToken ? {
        auth: {
            headers: {
                Authorization: `Bearer ${sanctumToken}`,
                Accept: 'application/json',
            },
        },
    } : {}),
});

window.Alpine = Alpine;

Alpine.start();
