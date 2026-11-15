import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('alpinejs')) return 'alpine';
                        if (id.includes('sweetalert2')) return 'sweetalert2';
                        if (id.includes('chart.js')) return 'chartjs';
                        return 'vendor';
                    }
                },
            },
        },
    },
});
