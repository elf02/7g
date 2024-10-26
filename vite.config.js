import { defineConfig, loadEnv } from 'vite'
import autoprefixer from 'autoprefixer'
import SG from './vite-plugin-7g'
import globImporter from 'node-sass-glob-importer'
import FullReload from 'vite-plugin-full-reload'
import fs from 'fs'

const dest = './dist';

const entries = [
    './assets/main.js',
    './assets/main.scss',
    './assets/editor.scss'
];

const watchFiles = [
    './*.php',
    './template-parts/**/*.php',
    './cpt/**/*.php',
    './inc/**/*.php',
    './src/**/*.php',
    './acf/**/*'
];

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const host = env.VITE_DEV_SERVER_HOST || 'http://localhost:3000';
    const isSecure = host.indexOf('https://') === 0 && (env.VITE_DEV_SERVER_KEY || env.VITE_DEV_SERVER_CERT);

    return {
        base: './',
        css: {
            devSourcemap: true,
            preprocessorOptions: {
                scss: {
                    importer: globImporter()
                }
            },
            postcss: {
                plugins: [autoprefixer()]
            }
        },
        resolve: {
            alias: {
                '@': __dirname
            }
        },
        plugins: [SG({ dest, host }), FullReload(watchFiles)],
        server: {
            https: isSecure
                ? {
                    key: fs.readFileSync(env.VITE_DEV_SERVER_KEY),
                    cert: fs.readFileSync(env.VITE_DEV_SERVER_CERT)
                }
                : false,
            host: 'localhost'
        },
        build: {
            manifest: true,
            outDir: dest,
            rollupOptions: {
                input: entries
            }
        }
    }
})