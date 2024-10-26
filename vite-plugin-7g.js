import path from 'path'
import fs from 'fs'

let exitHandlersBound = false;
export default function ({ dest, host }) {
    const hotFile = path.join(dest, 'hot');
    let viteDevServerUrl, resolvedConfig;

    return {
        name: '7g',
        enforce: 'post',
        config: () => {
            return {
                publicDir: false,
                server: {
                    origin: '__7g_vite_placeholder__'
                }
            }
        },
        configResolved(config) {
            resolvedConfig = config;
        },
        transform(code) {
            if (resolvedConfig.command === 'serve') {
                return code.replace(/__7g_vite_placeholder__/g, viteDevServerUrl);
            }
        },
        configureServer(server) {
            const appUrl = host

            server.httpServer?.on('listening', () => {
                const address = server.httpServer?.address();

                if (typeof address === 'object') {
                    viteDevServerUrl = resolveDevServerUrl(address, server.config);
                    fs.writeFileSync(hotFile, viteDevServerUrl);

                    setTimeout(() => {
                        const isHttps = host.indexOf('https://') === 0;
                        const hasCertificates = server.httpServer.key && server.httpServer.cert;

                        if (isHttps && !hasCertificates) {
                            server.config.logger.info('  ➜ Please define VITE_DEV_SERVER_KEY and VITE_DEV_SERVER_CERT inside a “.env” file in the theme folder to enable ssl support for the vite dev server.');
                        }

                        server.config.logger.info(`  ➜ APP_URL: ${appUrl.replace(/:(\d+)/, (_, port) => `:${port}`)}`);
                    }, 100)
                }
            })

            if (!exitHandlersBound) {
                const clean = () => {
                    if (fs.existsSync(hotFile)) {
                        fs.rmSync(hotFile);
                    }
                }

                process.on('exit', clean);
                process.on('SIGINT', () => process.exit(130));
                process.on('SIGTERM', () => process.exit(143));
                process.on('SIGHUP', () => process.exit(129));

                exitHandlersBound = true;
            }

            return () => server.middlewares.use((req, res, next) => {
                if (req.url === '/index.html') {
                    res.statusCode = 404;
                    res.end(`please open ${appUrl}`);
                }
                next();
            });
        }
    }
}

function resolveDevServerUrl(address, config) {
    const configHmrProtocol = typeof config.server.hmr === 'object' ? config.server.hmr.protocol : null;
    const clientProtocol = configHmrProtocol ? (configHmrProtocol === 'wss' ? 'https' : 'http') : null;
    const serverProtocol = config.server.https ? 'https' : 'http';
    const protocol = clientProtocol ?? serverProtocol;
    const isIpv6 = (address) => address.family === 'IPv6' || address.family === 6;

    const configHmrHost = typeof config.server.hmr === 'object' ? config.server.hmr.host : null;
    const configHost = typeof config.server.host === 'string' ? config.server.host : null;
    const serverAddress = isIpv6(address) ? `[${address.address}]` : address.address;
    const host = configHmrHost ?? configHost ?? serverAddress;

    const configHmrClientPort = typeof config.server.hmr === 'object' ? config.server.hmr.clientPort : null;
    const port = configHmrClientPort ?? address.port;

    return `${protocol}://${host}:${port}`;
}
