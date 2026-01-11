import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue(), // مهم لو هتستخدم Vue أو حتى لو مثبت Vue plugin
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        manifest: true,
        emptyOutDir: true,
        rollupOptions: {
            rollupOptions: {
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                ],
            },
        },
    },
});
