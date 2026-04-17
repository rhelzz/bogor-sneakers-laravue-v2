<template>
    <div class="h-screen overflow-hidden bg-zinc-100 text-[13px]">
        <!-- ── Sidebar ──────────────────────────────────────────────────── -->
        <aside
            class="fixed inset-y-0 left-0 z-40 flex flex-col overflow-hidden border-r border-zinc-800/60 bg-zinc-950"
            :style="{
                width: sidebarCollapsed ? '56px' : '240px',
                transition: 'width 320ms cubic-bezier(0.4, 0, 0.2, 1)',
            }"
        >
            <!-- Brand -->
            <div
                class="flex h-14 shrink-0 items-center justify-between border-b border-zinc-800/60 px-[14px]"
            >
                <div
                    class="overflow-hidden"
                    :style="{
                        width: sidebarCollapsed ? '0px' : '160px',
                        opacity: sidebarCollapsed ? 0 : 1,
                        transition:
                            'width 320ms cubic-bezier(0.4,0,0.2,1), opacity 200ms ease',
                    }"
                >
                    <p
                        class="text-[13px] leading-tight font-black tracking-[0.12em] text-zinc-100 uppercase"
                        style="font-family: 'Syne', sans-serif"
                    >
                        Bogor
                    </p>
                    <p
                        class="text-[13px] leading-tight font-black tracking-[0.12em] text-zinc-100 uppercase"
                        style="font-family: 'Syne', sans-serif"
                    >
                        Sneakers
                    </p>
                </div>
                <button
                    @click="sidebarCollapsed = !sidebarCollapsed"
                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-sm border border-zinc-800 bg-zinc-900/60 text-zinc-600 transition-all duration-150 hover:border-zinc-700 hover:bg-zinc-800/80 hover:text-zinc-300"
                >
                    <i
                        class="bi shrink-0 text-[13px] transition-transform duration-300"
                        :class="
                            sidebarCollapsed
                                ? 'bi-layout-sidebar'
                                : 'bi-layout-sidebar-reverse'
                        "
                    ></i>
                </button>
            </div>

            <!-- Nav Items -->
            <nav class="flex-1 overflow-x-hidden overflow-y-auto p-2">
                <div class="mt-1 mb-3">
                    <p
                        class="mb-1 overflow-hidden px-2 text-[10px] tracking-[0.15em] whitespace-nowrap text-zinc-600 uppercase"
                        :style="{
                            opacity: sidebarCollapsed ? 0 : 1,
                            maxHeight: sidebarCollapsed ? '0px' : '20px',
                            transition:
                                'opacity 200ms ease, max-height 320ms ease',
                        }"
                    >
                        Konten
                    </p>

                    <a
                        v-for="item in navItemsKonten"
                        :key="item.href"
                        :href="item.href"
                        class="group relative mb-0.5 flex h-9 items-center gap-3 overflow-hidden rounded-sm px-[11px] transition-colors duration-150"
                        :class="
                            isActive(item.route)
                                ? 'bg-zinc-800 text-zinc-100'
                                : 'text-zinc-500 hover:bg-zinc-800/50 hover:text-zinc-300'
                        "
                    >
                        <!-- Active left indicator -->
                        <span
                            v-if="isActive(item.route)"
                            class="absolute inset-y-1.5 left-0 w-[2px] rounded-r-full bg-zinc-300"
                        ></span>

                        <i
                            :class="[
                                'bi',
                                item.icon,
                                'w-[18px] shrink-0 text-center text-[15px]',
                            ]"
                        ></i>
                        <span
                            class="overflow-hidden text-[13px] font-medium whitespace-nowrap"
                            :style="{
                                width: sidebarCollapsed ? '0px' : '150px',
                                opacity: sidebarCollapsed ? 0 : 1,
                                transition:
                                    'width 320ms cubic-bezier(0.4,0,0.2,1), opacity 180ms ease',
                            }"
                        >
                            {{ item.label }}
                        </span>
                    </a>
                </div>

                <div class="mt-3 border-t border-zinc-800/60 pt-3">
                    <a
                        href="/"
                        target="_blank"
                        class="flex h-9 items-center gap-3 overflow-hidden rounded-sm px-[11px] text-zinc-600 transition-colors duration-150 hover:bg-zinc-800/50 hover:text-zinc-400"
                    >
                        <i
                            class="bi bi-arrow-up-right-circle w-[18px] shrink-0 text-center text-[15px]"
                        ></i>
                        <span
                            class="overflow-hidden text-[13px] font-medium whitespace-nowrap"
                            :style="{
                                width: sidebarCollapsed ? '0px' : '150px',
                                opacity: sidebarCollapsed ? 0 : 1,
                                transition:
                                    'width 320ms cubic-bezier(0.4,0,0.2,1), opacity 180ms ease',
                            }"
                        >
                            Lihat Website
                        </span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- ── Header ──────────────────────────────────────────────────── -->
        <header
            class="fixed inset-x-0 top-0 z-30 flex h-14 items-center justify-between border-b border-zinc-800/60 bg-zinc-950 px-5"
            :style="{
                left: sidebarCollapsed ? '56px' : '240px',
                transition: 'left 320ms cubic-bezier(0.4, 0, 0.2, 1)',
            }"
        >
            <!-- Left -->
            <div class="flex items-center gap-3">
                <div class="h-1 w-1 shrink-0 rounded-full bg-zinc-700"></div>
                <span
                    class="text-[11px] tracking-[0.15em] text-zinc-500 uppercase"
                    style="font-family: 'Syne', sans-serif"
                >
                    Dashboard
                </span>
            </div>

            <!-- Right -->
            <div class="flex items-center gap-1.5">
                <button
                    class="flex h-8 w-8 items-center justify-center rounded-sm text-zinc-500 transition-colors hover:bg-zinc-800/60 hover:text-zinc-300"
                >
                    <i class="bi bi-bell text-[14px]"></i>
                </button>

                <div class="mx-1 h-4 w-px bg-zinc-800"></div>

                <button
                    class="flex h-8 w-8 items-center justify-center rounded-sm bg-zinc-800 text-[12px] font-bold text-zinc-300 ring-1 ring-zinc-700/60 transition-all hover:bg-zinc-700 hover:text-zinc-100"
                    style="font-family: 'Syne', sans-serif"
                >
                    A
                </button>
            </div>
        </header>

        <!-- ── Main Content ────────────────────────────────────────────── -->
        <main
            class="pt-14"
            :style="{
                marginLeft: sidebarCollapsed ? '56px' : '240px',
                transition: 'margin-left 320ms cubic-bezier(0.4, 0, 0.2, 1)',
            }"
        >
            <div class="h-[calc(100vh-56px)] overflow-y-auto p-4 lg:p-6">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const sidebarCollapsed = ref(false);

// Load sidebar state from localStorage
onMounted(() => {
    const saved = localStorage.getItem('sidebarCollapsed');
    if (saved !== null) {
        sidebarCollapsed.value = JSON.parse(saved);
    }
});

// Save sidebar state to localStorage whenever it changes
watch(sidebarCollapsed, (newValue) => {
    localStorage.setItem('sidebarCollapsed', JSON.stringify(newValue));
});

const navItemsKonten = [
    {
        href: '/admin/carousel-home',
        icon: 'bi-images',
        label: 'Carousel Home',
        route: 'carousel-home',
    },
    {
        href: '/admin/tiktok-feed',
        icon: 'bi-tiktok',
        label: 'TikTok Feed',
        route: 'tiktok-feed',
    },
    {
        href: '/admin/galeri-karya',
        icon: 'bi-grid-3x3-gap',
        label: 'Galeri Karya',
        route: 'galeri-karya',
    },
    {
        href: '/admin/katalog',
        icon: 'bi-shop',
        label: 'Katalog',
        route: 'admin/katalog',
    },
    {
        href: '/admin/pre-order-home',
        icon: 'bi-hourglass-split',
        label: 'Pre-Order Home',
        route: 'pre-order-home',
    },
];

const isActive = (route: string) => {
    if (!route) return false;
    return page.url.includes(route);
};
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&display=swap');
</style>
