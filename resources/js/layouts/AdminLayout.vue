<template>
    <div class="h-screen overflow-hidden bg-shironeri text-[13px] text-sumi/90">
        <!-- Header -->
        <header
            class="fixed inset-x-0 top-0 z-40 border-b border-sumi/20 bg-sumi text-washi"
        >
            <div class="flex h-16 items-center justify-between px-4 lg:px-6">
                <!-- Logo -->
                <div class="flex items-center gap-2.5">
                    <i class="bi bi-image text-xl text-matcha"></i>
                    <div>
                        <h1 class="font-heading text-base font-bold">
                            Bogor Sneakers
                        </h1>
                        <p class="text-[11px] text-washi/60">Dashboard Admin</p>
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-2">
                    <button
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-washi/10 text-sm transition-all hover:bg-washi/20"
                    >
                        <i class="bi bi-bell text-washi"></i>
                    </button>
                    <button
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-matcha text-xs font-bold text-sumi transition-all hover:bg-matcha/80"
                    >
                        A
                    </button>
                </div>
            </div>
        </header>

        <!-- Sidebar -->
        <aside
            class="fixed top-16 bottom-0 left-0 z-30 w-60 border-r border-sumi/20 bg-sumi text-washi"
        >
            <nav class="space-y-1.5 p-4">
                <a
                    href="/admin/carousel-home"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                    :class="{
                        'bg-matcha/20 text-matcha': isActive('carousel-home'),
                    }"
                >
                    <i class="bi bi-images text-base"></i>
                    <span>Carousel Home</span>
                </a>

                <a
                    href="/admin/tiktok-feed"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                    :class="{
                        'bg-matcha/20 text-matcha': isActive('tiktok-feed'),
                    }"
                >
                    <i class="bi bi-tiktok text-base"></i>
                    <span>TikTok Feed</span>
                </a>

                <a
                    href="/admin/galeri-karya"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                    :class="{
                        'bg-matcha/20 text-matcha': isActive('galeri-karya'),
                    }"
                >
                    <i class="bi bi-grid-3x3-gap text-base"></i>
                    <span>Galeri Karya</span>
                </a>

                <a
                    href="/admin/katalog"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                    :class="{
                        'bg-matcha/20 text-matcha': isActive('admin/katalog'),
                    }"
                >
                    <i class="bi bi-shop text-base"></i>
                    <span>Katalog</span>
                </a>

                <a
                    href="/admin/pre-order-home"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                    :class="{
                        'bg-matcha/20 text-matcha': isActive('pre-order-home'),
                    }"
                >
                    <i class="bi bi-hourglass-split text-base"></i>
                    <span>Pre-Order Home</span>
                </a>

                <a
                    href="/"
                    target="_blank"
                    class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                >
                    <i class="bi bi-house text-base"></i>
                    <span>Lihat Website</span>
                </a>
            </nav>

            <div class="absolute right-4 bottom-4 left-4">
                <div class="rounded-lg bg-washi/10 p-3 text-center">
                    <p class="mb-1 text-[11px] text-washi/60">Bogor Sneakers</p>
                    <p class="text-[11px] text-washi/40">Est. 2019</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-60 pt-16">
            <div class="h-[calc(100vh-64px)] overflow-y-auto p-4 lg:p-6">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const isActive = (route: string) => {
    return page.url.includes(route);
};
</script>

<style scoped>
/* Smooth transitions */
a {
    text-decoration: none;
}
</style>
