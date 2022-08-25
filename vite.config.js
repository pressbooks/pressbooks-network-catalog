import {defineConfig} from 'vite';
import {resolve} from 'path';
import liveReload from 'vite-plugin-live-reload'

export default defineConfig(() => {
  return {
    server: {
      cors: true,
      strictPort: true,
      port: 3000,
      hmr: {
        port: 3000,
        host: 'localhost',
        protocol: 'ws',
      },
    },
    plugins: [
      liveReload(
        __dirname + '/src/**/*.php',
        __dirname + '/resources/**/*.blade.php',
        __dirname + '/assets/**/*.css',
        __dirname + '/assets/**/*.js'
      ),
    ],
    root: '',
    base: process.env.NODE_ENV === 'development'
      ? '/'
      : '/dist/',
    build: {
      // output dir for production build
      outDir: resolve(__dirname, './dist'),
      target: 'es2018',
      manifest: true,
      emptyOutDir: true,
      rollupOptions: {
        input: {
          app: resolve(__dirname + '/assets/js/app.js')
        }
      },
      minify: true,
      write: true
    },
  };
});
