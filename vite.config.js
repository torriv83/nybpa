import {defineConfig} from 'vite';
import laravel, {refreshPaths} from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                `resources/css/filament/landslag/theme.css`,
                `resources/css/filament/privat/theme.css`,
            ],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
                'app/Filament/**',
                'app/Providers/Filament/**',
            ],
        }),
    ],

    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
