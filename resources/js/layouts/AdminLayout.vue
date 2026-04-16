<template>
    <div class="admin-shell">
        <header class="admin-topbar">
            <div class="admin-topbar-inner">
                <div class="flex items-center gap-2 lg:gap-3">
                    <button
                        type="button"
                        class="admin-icon-button lg:hidden"
                        @click="toggleSidebar"
                    >
                        <i class="bi bi-list text-lg"></i>
                    </button>

                    <a href="/admin/carousel-home" class="admin-brand">
                        <span class="admin-brand-mark">
                            <i class="bi bi-grid-1x2-fill"></i>
                        </span>
                        <span>
                            <strong>Bogor Sneakers</strong>
                            <small>Admin Dashboard</small>
                        </span>
                    </a>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        href="/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="admin-topbar-link"
                    >
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>Lihat Website</span>
                    </a>
                    <span class="admin-avatar">A</span>
                </div>
            </div>
        </header>

        <button
            v-if="isSidebarOpen"
            type="button"
            class="admin-sidebar-backdrop lg:hidden"
            @click="closeSidebar"
            aria-label="Tutup sidebar"
        ></button>

        <aside
            :class="[
                'admin-sidebar',
                isSidebarOpen ? 'admin-sidebar-open' : '',
            ]"
        >
            <nav class="admin-sidebar-nav">
                <a
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="admin-nav-item"
                    :class="{
                        'admin-nav-item-active': isActive(item.match),
                    }"
                    @click="closeSidebar"
                >
                    <i :class="item.icon"></i>
                    <span>{{ item.label }}</span>
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <p class="text-xs font-semibold text-slate-400">
                    Bogor Sneakers
                </p>
                <p class="text-xs text-slate-500">Admin Panel 2026</p>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-main-scroll">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

type NavigationItem = {
    href: string;
    match: string;
    label: string;
    icon: string;
};

const page = usePage();
const isSidebarOpen = ref(false);

const navItems: NavigationItem[] = [
    {
        href: '/admin/carousel-home',
        match: 'carousel-home',
        label: 'Carousel Home',
        icon: 'bi bi-images',
    },
    {
        href: '/admin/tiktok-feed',
        match: 'tiktok-feed',
        label: 'TikTok Feed',
        icon: 'bi bi-tiktok',
    },
    {
        href: '/admin/galeri-karya',
        match: 'galeri-karya',
        label: 'Galeri Karya',
        icon: 'bi bi-grid-3x3-gap',
    },
    {
        href: '/admin/katalog',
        match: '/admin/katalog',
        label: 'Katalog',
        icon: 'bi bi-shop',
    },
    {
        href: '/admin/pre-order-home',
        match: 'pre-order-home',
        label: 'Pre-Order Home',
        icon: 'bi bi-hourglass-split',
    },
];

const isActive = (route: string) => {
    return page.url.includes(route);
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const handleWindowResize = () => {
    if (window.innerWidth >= 1024) {
        isSidebarOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('resize', handleWindowResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleWindowResize);
});
</script>
