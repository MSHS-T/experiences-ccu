import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
        cors: {
            origin: /^https?:\/\/(?:(?:[^:]+\.)?localhost|experiences-ccu\.test|127\.0\.0\.1|\[::1\])(?::\d+)?$/,
        },
    },
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/filament/admin/theme.css",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
    ],
});
