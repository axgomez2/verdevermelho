import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import { homedir } from 'os';
import { resolve } from 'path';

// Herd SSL Certificate Paths
const certDir = resolve(homedir(), '.herd/opt/ssl');
const hasCerts = fs.existsSync(certDir);

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        // Configuração HTTPS com certificados do Herd, se disponíveis
        https: hasCerts ? {
            key: resolve(certDir, 'herd.key'),
            cert: resolve(certDir, 'herd.crt'),
        } : false,
        host: 'sasembaixada.test',
        hmr: {
            host: 'sasembaixada.test',
        },
    },
});
