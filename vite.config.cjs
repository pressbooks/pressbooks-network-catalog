

import {defineConfig} from 'vite';

export default defineConfig(() => {
  return {
    server: {
      port: 3001
    },
    build: {
      rollupOptions: {
        output: {
          entryFileNames: 'assets/[name].js',
          chunkFileNames: 'assets/[name].js',
          assetFileNames: 'assets/[name].[ext]'
        }
      }
    }
  };
});
