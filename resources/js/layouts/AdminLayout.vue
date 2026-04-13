<template>
    <div class="min-h-screen bg-shironeri">
        <!-- Header -->
        <header
            class="sticky top-0 z-40 border-b border-sumi/20 bg-sumi text-washi"
        >
            <div class="flex items-center justify-between px-6 py-4 lg:px-8">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <i class="bi bi-image text-2xl text-matcha"></i>
                    <div>
                        <h1 class="font-heading text-lg font-bold">
                            Bogor Sneakers
                        </h1>
                        <p class="text-xs text-washi/60">Dashboard Admin</p>
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-4">
                    <button
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-washi/10 transition-all hover:bg-washi/20"
                    >
                        <i class="bi bi-bell text-washi"></i>
                    </button>
                    <button
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-matcha font-bold text-sumi transition-all hover:bg-matcha/80"
                    >
                        A
                    </button>
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar -->
            <aside
                class="sticky top-18 min-h-[calc(100vh-72px)] w-64 border-r border-sumi/20 bg-sumi text-washi"
            >
                <nav class="space-y-2 p-6">
                    <!-- Navigation Items -->
                    <a
                        href="/admin/carousel-home"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                        :class="{
                            'bg-matcha/20 text-matcha':
                                isActive('carousel-home'),
                        }"
                    >
                        <i class="bi bi-images text-lg"></i>
                        <span>Carousel Home</span>
                    </a>

                    <a
                        href="/admin/tiktok-feed"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                        :class="{
                            'bg-matcha/20 text-matcha': isActive('tiktok-feed'),
                        }"
                    >
                        <i class="bi bi-tiktok text-lg"></i>
                        <span>TikTok Feed</span>
                    </a>

                    <a
                        href="/admin/galeri-karya"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                        :class="{
                            'bg-matcha/20 text-matcha':
                                isActive('galeri-karya'),
                        }"
                    >
                        <i class="bi bi-grid-3x3-gap text-lg"></i>
                        <span>Galeri Karya</span>
                    </a>

                    <a
                        href="/admin/katalog"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                        :class="{
                            'bg-matcha/20 text-matcha': isActive('admin/katalog'),
                        }"
                    >
                        <i class="bi bi-shop text-lg"></i>
                        <span>Katalog</span>
                    </a>

                    <a
                        href="/"
                        target="_blank"
                        class="flex items-center gap-3 rounded-lg px-4 py-3 font-medium text-washi/80 transition-all hover:bg-washi/10 hover:text-washi"
                    >
                        <i class="bi bi-house text-lg"></i>
                        <span>Lihat Website</span>
                    </a>
                </nav>

                <!-- Footer Info -->
                <div class="absolute right-6 bottom-6 left-6">
                    <div class="rounded-lg bg-washi/10 p-4 text-center">
                        <p class="mb-2 text-xs text-washi/60">Bogor Sneakers</p>
                        <p class="text-xs text-washi/40">Est. 2019</p>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <div class="p-6 lg:p-8">
                    <slot />
                </div>
            </main>
        </div>
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
