# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Bogor Sneakers is a Laravel 13 + Vue 3 e-commerce app for a shoe customization business. It uses Inertia.js as the bridge between backend and frontend — Laravel handles routing and data, Vue handles rendering (no separate API for pages).

## Commands

### Development
```bash
# Start dev server (runs Vite + Laravel simultaneously)
composer run dev

# Or separately:
npm run dev        # Vite HMR
php artisan serve  # Laravel
```

### Build
```bash
npm run build       # Production build
npm run build:ssr   # SSR build
```

### Testing
```bash
# PHP tests
php artisan test
composer run test

# JS/TS tests
npm run test          # Vitest (watch mode)
npm run test:ui       # Vitest with browser UI
npm run test:coverage # Coverage report

# Single PHP test
php artisan test --filter TestClassName
php artisan test tests/Feature/SpecificTest.php

# Single JS test
npx vitest run path/to/test.spec.ts
```

### Linting & Formatting
```bash
# PHP (Laravel Pint)
composer run lint           # Fix issues
composer run lint:check     # Check only

# JS/TS
npm run lint       # ESLint fix
npm run format     # Prettier fix
npm run types:check # Vue TSC type check

# Run all checks (CI)
composer run ci:check
```

## Architecture

### Inertia.js Stack
- Laravel routes return `Inertia::render('PageName', $props)` — not JSON
- Vue pages live in `resources/js/pages/` and receive typed Laravel props
- `app/Http/Middleware/HandleInertiaRequests.php` shares global data (auth, cart count) with every page
- Wayfinder generates type-safe route helpers from Laravel routes into `resources/js/wayfinder/`

### Key Data Flows

**Cart**: Session-based cart identified by a token cookie (`AssignCartToken` middleware). `CartService` handles all cart mutations. Frontend uses a Pinia store (`resources/js/stores/`) that mirrors backend cart state.

**Custom Studio**: The shoe design tool at `/studio-custom` uses Konva.js (canvas) heavily. The logic is split across three composables:
- `useKonvaRenderer` — canvas rendering and element manipulation
- `useStudioStore` — state management for design elements
- `useStudioHistory` — undo/redo history

Studio assets (SVG shoe templates, design elements) are managed via `StudioAssetController` and `ShoeVariantSvg` models.

**Orders/Checkout**: `POST /api/checkout` → `OrderController@checkout`. Shipping rates come from an external API configured in `.env` (`SHIPPING_COST_URL`, `SHIPPING_DESTINATION_URL`).

**Admin**: All admin routes are under `/admin` prefix. No separate auth guard is currently shown in routes — check middleware in `routes/web.php`.

### Type Safety
- TypeScript strict mode with `@/*` alias mapping to `resources/js/`
- PHP model shapes are replicated in `resources/js/types/` (e.g., `catalog.ts`, `studio.ts`)
- Keep these in sync when changing Eloquent models

### Styling
- Tailwind CSS 4 with a Material Design 3 color palette configured in `tailwind.config.js`
- Custom fonts: Inter (body), Lexend (headings)
- Prettier auto-sorts Tailwind classes — run `npm run format` after editing templates

### Database
- MySQL in production, SQLite in-memory for tests (`phpunit.xml`)
- Models of note: `ShoeVariant` has an SVG field and belongs to `ShoeModel` → `ShoeType` hierarchy
- `CatalogImage` uses ordered gallery slots; reorder endpoint exists in admin routes

## Environment
Copy `.env.example` and configure:
- `DB_DATABASE=bogor_sneakers` (MySQL)
- `TIKTOK_*` keys for TikTok feed integration
- `SHIPPING_COST_URL` / `SHIPPING_DESTINATION_URL` for shipping rate API
