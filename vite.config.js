import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue';
import {resolve} from 'path';

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [vue()],
    base: '',
    build: {
        outDir: 'dist',
        rollupOptions: {
            input: resolve(__dirname, 'src/main.js'),
            output: {
                entryFileNames: 'main.js',
                assetFileNames: 'assets/[name].[ext]',
            },
        },
    },
});
