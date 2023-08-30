import {defineConfig} from 'vite';
import {resolve} from 'path'

const twigRefreshPlugin = {
    name: 'twig-refresh',
    configureServer ({watcher, ws}) {
        watcher.add(resolve('templates/**/*.twig'))
        watcher.on('change', function (path) {
            if (path.endsWith('.twig')) {
                ws.send({
                    type: 'full-reload'
                })
            }
        })
    }
}

export default defineConfig({
    plugins: [twigRefreshPlugin],
    root: './assets',
    base: '/assets/',
    server: {
        host: true,
        port: 3000,
        watch: {
            disableGlobbing: false
        }
    },
    build: {
        assetsDir: '',
        outDir: '../public/assets/',
        rollupOptions: {
            input: {
                'main.js': './assets/main.js'
            }
        },
        manifest: true
    }
})