import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue';
import {resolve} from 'path';

export default defineConfig({
    plugins: [vue()],
    base: '',
    build: {
        outDir: 'dist',
        rollupOptions: {
            input: resolve(__dirname, 'resources/main.js'),
            output: {
                entryFileNames: 'main.js',
                assetFileNames: 'assets/[name].[ext]',
            },
            external: ['@wordpress/i18n'],
        },
    },
    optimizeDeps: {
        include: ['@wordpress/i18n'],
    },
});