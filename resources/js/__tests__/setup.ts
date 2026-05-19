import { vi } from 'vitest'

// Global mock setup untuk File API
globalThis.File = class MockFile {
  constructor(
    public content: any[],
    public filename: string,
    public options: any = {}
  ) {}
  size = 0
} as any

// Mock window.matchMedia jika diperlukan
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: vi.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(),
    removeListener: vi.fn(),
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
})
