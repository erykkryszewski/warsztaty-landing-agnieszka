import { resolve } from 'path';
import { defineConfig } from 'vite';

export default defineConfig({
  publicDir: false,
  build: {
    outDir: 'public/assets/build',
    emptyOutDir: true,
    manifest: false,
    rollupOptions: {
      input: {
        site: resolve(__dirname, 'resources/js/site.js'),
        admin: resolve(__dirname, 'resources/js/admin.js')
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/chunks/[name].js',
        assetFileNames: ({ name }) => {
          if (name && name.endsWith('.css')) {
            return 'css/[name][extname]';
          }

          return 'assets/[name][extname]';
        }
      }
    }
  }
});
