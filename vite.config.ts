import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    define: {
        // Replace process.env.NODE_ENV for browser compatibility
        'process.env.NODE_ENV': JSON.stringify('production'),
    },
    build: {
        lib: {
            entry: resolve(__dirname, 'resources/js/index.ts'),
            name: 'AcceladeFilters',
            fileName: (format) => `filters.${format}.js`,
            formats: ['es', 'iife'],
        },
        outDir: 'dist',
        sourcemap: true,
        minify: 'terser',
        rollupOptions: {
            output: {
                globals: {},
            },
        },
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
});
