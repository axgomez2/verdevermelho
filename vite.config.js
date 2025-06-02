import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/wishlist-cart.js',
                'resources/js/wishlist-item.js',
                'resources/js/wantlist-item.js',
                'resources/js/notification-checker.js'
            ],
            refresh: true,
        }),
    ],
});
