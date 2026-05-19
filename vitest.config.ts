import path from 'path'
import vue from '@vitejs/plugin-vue'
import { defineConfig } from 'vitest/config'

export default defineConfig({
  plugins: [vue()],
  test: {
    globals: true,
    environment: 'jsdom',
    include: ['resources/js/__tests__/**/*.test.ts', 'resources/js/**/*.test.ts'],
    setupFiles: ['resources/js/__tests__/setup.ts'],
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      include: ['resources/js/**/*.ts', 'resources/js/**/*.vue'],
      exclude: ['resources/js/__tests__/**', 'node_modules/**', 'resources/js/**/index.ts'],
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js'),
    },
  },
})
