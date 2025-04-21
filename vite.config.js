import {defineConfig} from 'vite';
import path from 'path';
import {viteStaticCopy} from 'vite-plugin-static-copy';

export default defineConfig({
    build: {
        sourcemap: true,
        rollupOptions: {
            input: {
                'css/style': path.resolve(__dirname, 'resources/css/style.css'),
                'js/functions': path.resolve(__dirname, 'resources/js/functions.js'),
            },
            output: {
                entryFileNames: '[name].min.js',
                assetFileNames: '[name].min.css',
            }
        },
        outDir: 'assets',
        emptyOutDir: false
    },
    plugins: [
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/tablesort/dist/tablesort.min.js',
                    dest: 'js/lib'
                },
                {
                    src: 'node_modules/tablesort/dist/sorts/tablesort.number.min.js',
                    dest: 'js/lib'
                }
            ]
        })
    ],
    publicDir: false
});
