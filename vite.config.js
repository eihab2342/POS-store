import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/filament/app.css",
                "resources/js/filament/app.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
    ],
});
