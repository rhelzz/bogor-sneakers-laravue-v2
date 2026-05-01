<template>
    <div
        class="flex h-screen overflow-hidden bg-slate-50 font-['Source_Sans_Pro'] text-slate-800 antialiased"
    >
        <!-- Sidebar -->
        <aside
            :class="[
                'relative z-20 flex shrink-0 flex-col border-r border-white/5 bg-[#15161a] text-slate-300 shadow-2xl transition-all duration-300 ease-in-out',
                isSidebarOpen ? 'w-72' : 'w-20',
            ]"
        >
            <!-- Sidebar Header -->
            <div
                class="relative flex h-20 shrink-0 items-center justify-between px-5"
            >
                <h1
                    v-if="isSidebarOpen"
                    class="ml-2 truncate font-['Montserrat'] text-xl font-extrabold tracking-wide text-white transition-opacity duration-300 ease-in-out"
                >
                    ADMIN<span class="text-indigo-500">PANEL</span>
                </h1>

                <button
                    @click="toggleSidebar"
                    class="ml-auto rounded-xl p-2 text-slate-400 transition-all duration-200 hover:bg-white/5 hover:text-white focus:outline-none"
                    title="Toggle Sidebar"
                >
                    <!-- Menu Icon -->
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path
                            v-if="isSidebarOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h7"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav
                class="no-scrollbar flex-1 space-y-3 overflow-y-auto px-3 py-6"
            >
                <!-- Dashboard -->
                <Link
                    :href="dashboard.url()"
                    class="group flex items-center rounded-xl px-3 py-3.5 transition-all duration-200 hover:bg-white/5 hover:text-white"
                    :class="{ 'justify-center': !isSidebarOpen }"
                >
                    <div
                        class="rounded-lg bg-white/5 p-2 transition-colors group-hover:bg-indigo-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                            />
                        </svg>
                    </div>
                    <span
                        v-if="isSidebarOpen"
                        class="ml-4 truncate font-semibold tracking-wide"
                    >
                        Dashboard
                    </span>
                </Link>

                <!-- Manajemen Produk -->
                <div class="space-y-1">
                    <button
                        @click="toggleProductMenu"
                        class="group flex w-full items-center rounded-xl px-3 py-3.5 transition-all duration-200 hover:bg-white/5 hover:text-white focus:outline-none"
                        :class="{
                            'justify-center': !isSidebarOpen,
                            'bg-white/10 text-white':
                                isProductMenuOpen && isSidebarOpen,
                        }"
                    >
                        <div
                            class="rounded-lg bg-white/5 p-2 transition-colors group-hover:bg-indigo-600"
                            :class="{
                                'bg-indigo-600 text-white':
                                    isProductMenuOpen && isSidebarOpen,
                            }"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                />
                            </svg>
                        </div>
                        <span
                            v-if="isSidebarOpen"
                            class="ml-4 flex-1 truncate text-left font-semibold tracking-wide"
                        >
                            Manajemen Produk
                        </span>
                        <svg
                            v-if="isSidebarOpen"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 shrink-0 text-slate-500 transition-transform duration-300 ease-in-out group-hover:text-white"
                            :class="
                                isProductMenuOpen
                                    ? 'rotate-180 text-white'
                                    : 'rotate-0'
                            "
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <!-- Submenu Produk -->
                    <div
                        v-show="isSidebarOpen"
                        class="grid overflow-hidden transition-[grid-template-rows] duration-300 ease-in-out"
                        :style="{
                            gridTemplateRows: isProductMenuOpen ? '1fr' : '0fr',
                        }"
                    >
                        <div class="overflow-hidden">
                            <div class="mt-1 space-y-1 py-2 pr-2 pl-14">
                                <Link
                                    v-for="item in productSubMenuItems"
                                    :key="item.href"
                                    :href="item.href"
                                    :class="getSubMenuClasses(item.href)"
                                >
                                    {{ item.label }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manajemen Konten -->
                <div class="space-y-1">
                    <button
                        @click="toggleContentMenu"
                        class="group flex w-full items-center rounded-xl px-3 py-3.5 transition-all duration-200 hover:bg-white/5 hover:text-white focus:outline-none"
                        :class="{
                            'justify-center': !isSidebarOpen,
                            'bg-white/10 text-white':
                                isContentMenuOpen && isSidebarOpen,
                        }"
                    >
                        <div
                            class="rounded-lg bg-white/5 p-2 transition-colors group-hover:bg-indigo-600"
                            :class="{
                                'bg-indigo-600 text-white':
                                    isContentMenuOpen && isSidebarOpen,
                            }"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                        </div>
                        <span
                            v-if="isSidebarOpen"
                            class="ml-4 flex-1 truncate text-left font-semibold tracking-wide"
                        >
                            Manajemen Konten
                        </span>
                        <svg
                            v-if="isSidebarOpen"
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 shrink-0 text-slate-500 transition-transform duration-300 ease-in-out group-hover:text-white"
                            :class="
                                isContentMenuOpen
                                    ? 'rotate-180 text-white'
                                    : 'rotate-0'
                            "
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <!-- Submenu -->
                    <div
                        v-show="isSidebarOpen"
                        class="grid overflow-hidden transition-[grid-template-rows] duration-300 ease-in-out"
                        :style="{
                            gridTemplateRows: isContentMenuOpen ? '1fr' : '0fr',
                        }"
                    >
                        <div class="overflow-hidden">
                            <div class="mt-1 space-y-1 py-2 pr-2 pl-14">
                                <Link
                                    v-for="item in subMenuItems"
                                    :key="item.href"
                                    :href="item.href"
                                    :class="getSubMenuClasses(item.href)"
                                >
                                    {{ item.label }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- User Profile -->
            <div class="shrink-0 border-t border-white/5 bg-[#15161a] p-4">
                <div
                    class="flex items-center"
                    :class="{ 'justify-center': !isSidebarOpen }"
                >
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-600 shadow-lg"
                    >
                        <span
                            class="font-['Montserrat'] text-lg font-extrabold text-white"
                            >A</span
                        >
                    </div>
                    <div v-if="isSidebarOpen" class="ml-4 truncate">
                        <p class="truncate text-sm font-bold text-white">
                            Administrator
                        </p>
                        <p
                            class="mt-0.5 truncate text-xs font-semibold text-slate-500"
                        >
                            admin@bogorsneakers.com
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="relative flex flex-1 flex-col overflow-hidden bg-slate-50">
            <!-- Header -->
            <header
                class="sticky top-0 z-10 flex h-20 shrink-0 items-center border-b border-slate-200/80 bg-white/80 px-8 backdrop-blur-md"
            >
                <div class="flex flex-col">
                    <h2
                        class="font-['Montserrat'] text-2xl font-extrabold tracking-tight text-slate-800"
                    >
                        <slot name="header">Dashboard</slot>
                    </h2>
                    <!-- Optional Subtitle -->
                    <p
                        class="mt-0.5 hidden text-sm font-bold text-slate-500 sm:block"
                    >
                        Overview & Statistik Sistem
                    </p>
                </div>

                <div class="ml-auto flex items-center space-x-6">
                    <!-- Notification Button -->
                    <button
                        class="relative rounded-xl p-2.5 text-slate-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600 focus:outline-none"
                    >
                        <span
                            class="absolute top-2 right-2 h-2.5 w-2.5 rounded-full border-2 border-white bg-rose-500"
                        ></span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>
                    </button>
                </div>
            </header>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-auto p-8">
                <div class="mx-auto max-w-7xl">
                    <slot>
                        <!-- Default Content Placeholder -->
                        <div
                            class="rounded-2xl border border-slate-200/80 bg-white p-12 shadow-sm"
                        >
                            <div
                                class="flex flex-col items-center justify-center py-10 text-center"
                            >
                                <div
                                    class="mb-5 flex h-20 w-20 items-center justify-center rounded-2xl border border-slate-100 bg-slate-50 text-slate-400"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-10 w-10"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                        />
                                    </svg>
                                </div>
                                <h3
                                    class="font-['Montserrat'] text-xl font-extrabold text-slate-800"
                                >
                                    Tidak Ada Konten
                                </h3>
                                <p class="mt-2 font-semibold text-slate-500">
                                    Konten utama akan tampil di area ini.
                                    Gunakan
                                    <code
                                        class="mx-1 rounded-md bg-slate-100 px-1.5 py-0.5 text-sm text-slate-700"
                                        >&lt;slot&gt;</code
                                    >
                                    untuk menyisipkan.
                                </p>
                            </div>
                        </div>
                    </slot>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import {
    dashboard,
    carouselHome,
    preOrderHome,
    tiktokFeed,
    galeriKarya,
    whatsappAdmins,
    katalog,
} from '@/routes/admin';

const page = usePage();
const isSidebarOpen = ref(true);
const isContentMenuOpen = ref(true);
const isProductMenuOpen = ref(true);

const subMenuItems = computed(() => [
    { label: 'Carousel Home', href: carouselHome.url() },
    { label: 'Pre-order Home', href: preOrderHome.url() },
    { label: 'Tiktok Feed', href: tiktokFeed.url() },
    { label: 'Galeri Karya', href: galeriKarya.url() },
    { label: 'WhatsApp Admin', href: whatsappAdmins.url() },
]);

const productSubMenuItems = computed(() => [
    { label: 'Katalog', href: katalog.url() },
    { label: 'Kelola Model Sepatu', href: '/admin/model-sepatu' },
]);

const isUrlActive = (href: string) => {
    return page.url === href || page.url.startsWith(href + '?');
};

const getSubMenuClasses = (href: string) => {
    const active = isUrlActive(href);
    const baseClasses =
        'group relative flex items-center rounded-lg px-4 py-2.5 font-semibold transition-all duration-300 ease-in-out before:absolute before:top-0 before:-bottom-1 before:-left-6 before:w-px before:bg-white/10 before:transition-colors after:absolute after:top-1/2 after:-left-6 after:h-px after:w-4 after:bg-white/10 after:transition-colors group-hover:after:bg-indigo-500 first:before:-top-2 last:before:bottom-auto last:before:h-1/2 last:before:w-4 last:before:rounded-bl-xl last:before:border-b last:before:border-l last:before:border-white/10 last:before:bg-transparent group-hover:last:before:border-indigo-500 last:after:hidden hover:bg-white/5 hover:text-white';
    const stateClasses = active
        ? 'text-[15px] text-white'
        : 'text-sm text-slate-400';

    return `${baseClasses} ${stateClasses}`;
};

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;

    if (!isSidebarOpen.value) {
        isContentMenuOpen.value = false;
        isProductMenuOpen.value = false;
    }
};

const toggleContentMenu = () => {
    if (isSidebarOpen.value) {
        isContentMenuOpen.value = !isContentMenuOpen.value;
    } else {
        isSidebarOpen.value = true;
        isContentMenuOpen.value = true;
    }
};

const toggleProductMenu = () => {
    if (isSidebarOpen.value) {
        isProductMenuOpen.value = !isProductMenuOpen.value;
    } else {
        isSidebarOpen.value = true;
        isProductMenuOpen.value = true;
    }
};
</script>

<style scoped>
/* Custom utilitas jika scrollbar ingin dihilangkan pada sidebar */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
