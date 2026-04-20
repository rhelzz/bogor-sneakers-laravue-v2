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
                class="flex h-14 shrink-0 items-center justify-between border-b border-zinc-800/60 px-3.5"
            >
                <div
                    class="flex items-center gap-2 overflow-hidden"
                    :style="{
                        width: sidebarCollapsed ? '0px' : '160px',
                        opacity: sidebarCollapsed ? 0 : 1,
                        transition:
                            'width 320ms cubic-bezier(0.4,0,0.2,1), opacity 200ms ease',
                    }"
                >
                    <img
                        src="/logo-b-bogor.svg"
                        alt="Logo Bogor Sneakers"
                        class="h-8 w-8 shrink-0 rounded-lg border border-zinc-700/60 bg-zinc-900/80 p-1"
                        loading="eager"
                        decoding="async"
                    />
                    <div>
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
                            Sneaker
                        </p>
                    </div>
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
                        Menu
                    </p>

                    <div
                        v-for="group in navGroups"
                        :key="group.key"
                        class="mb-1 overflow-hidden rounded-sm"
                    >
                        <button
                            type="button"
                            class="group relative flex h-9 w-full items-center gap-3 overflow-hidden rounded-sm px-2.75 text-left transition-colors duration-150"
                            :class="
                                isGroupActive(group)
                                    ? 'bg-zinc-800 text-zinc-100'
                                    : 'text-zinc-500 hover:bg-zinc-800/50 hover:text-zinc-300'
                            "
                            @click="toggleGroup(group.key)"
                        >
                            <span
                                v-if="isGroupActive(group)"
                                class="absolute inset-y-1.5 left-0 w-0.5 rounded-r-full bg-zinc-300"
                            ></span>

                            <i
                                :class="[
                                    'bi',
                                    group.icon,
                                    'w-4.5 shrink-0 text-center text-[15px]',
                                ]"
                            ></i>
                            <span
                                class="overflow-hidden text-[13px] font-medium whitespace-nowrap"
                                :style="{
                                    width: sidebarCollapsed ? '0px' : '130px',
                                    opacity: sidebarCollapsed ? 0 : 1,
                                    transition:
                                        'width 320ms cubic-bezier(0.4,0,0.2,1), opacity 180ms ease',
                                }"
                            >
                                {{ group.label }}
                            </span>
                            <i
                                class="bi bi-chevron-down ml-auto shrink-0 text-[11px] text-zinc-500 transition-all duration-200"
                                :style="{
                                    opacity: sidebarCollapsed ? 0 : 1,
                                    transform:
                                        !sidebarCollapsed &&
                                        isGroupOpen(group.key)
                                            ? 'rotate(180deg)'
                                            : 'rotate(0deg)',
                                }"
                            ></i>
                        </button>

                        <div
                            class="space-y-0.5 overflow-hidden pl-2 transition-all duration-300"
                            :style="submenuStyle(group)"
                        >
                            <a
                                v-for="item in group.items"
                                :key="item.href"
                                :href="item.href"
                                class="group relative flex h-8 items-center gap-2 overflow-hidden rounded-sm pr-2 pl-6 transition-colors duration-150"
                                :class="
                                    isActive(item.route)
                                        ? 'bg-zinc-800/70 text-zinc-100'
                                        : 'text-zinc-500 hover:bg-zinc-800/50 hover:text-zinc-300'
                                "
                            >
                                <span
                                    class="h-1.5 w-1.5 shrink-0 rounded-full"
                                    :class="
                                        isActive(item.route)
                                            ? 'bg-zinc-200'
                                            : 'bg-zinc-700 group-hover:bg-zinc-500'
                                    "
                                ></span>
                                <span
                                    class="text-[12px] font-medium whitespace-nowrap"
                                >
                                    {{ item.label }}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-3 border-t border-zinc-800/60 pt-3">
                    <a
                        href="/"
                        target="_blank"
                        class="flex h-9 items-center gap-3 overflow-hidden rounded-sm px-2.75 text-zinc-600 transition-colors duration-150 hover:bg-zinc-800/50 hover:text-zinc-400"
                    >
                        <i
                            class="bi bi-arrow-up-right-circle w-4.5 shrink-0 text-center text-[15px]"
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
                <img
                    src="/logo-b-bogor.svg"
                    alt="Logo Bogor Sneakers"
                    class="h-7 w-7 rounded-md border border-zinc-700/70 bg-zinc-900/70 p-1"
                    loading="eager"
                    decoding="async"
                />
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

<script lang="ts">
import { usePage } from '@inertiajs/vue3';
import { defineComponent, onMounted, ref, watch } from 'vue';

type AdminNavItem = {
    href: string;
    label: string;
    route: string;
};

type AdminNavGroup = {
    key: string;
    label: string;
    icon: string;
    items: AdminNavItem[];
};

export default defineComponent({
    name: 'AdminLayout',
    setup() {
        const page = usePage();
        const sidebarCollapsed = ref(false);
        const openGroups = ref<Record<string, boolean>>({});

        const isActive = (route: string) => {
            if (!route) {
                return false;
            }

            return page.url.includes(route);
        };

        const navGroups: AdminNavGroup[] = [
            {
                key: 'custom-konten',
                label: 'Custom Konten',
                icon: 'bi-window-stack',
                items: [
                    {
                        href: '/admin/carousel-home',
                        label: 'Carousel Home',
                        route: 'carousel-home',
                    },
                    {
                        href: '/admin/tiktok-feed',
                        label: 'TikTok Feed',
                        route: 'tiktok-feed',
                    },
                    {
                        href: '/admin/galeri-karya',
                        label: 'Galeri Karya',
                        route: 'galeri-karya',
                    },
                    {
                        href: '/admin/pre-order-home',
                        label: 'Pre-Order Home',
                        route: 'pre-order-home',
                    },
                    {
                        href: '/admin/whatsapp-admins',
                        label: 'WhatsApp Admin',
                        route: 'whatsapp-admins',
                    },
                ],
            },
            {
                key: 'produk',
                label: 'Produk',
                icon: 'bi-shop',
                items: [
                    {
                        href: '/admin/katalog',
                        label: 'Katalog',
                        route: 'admin/katalog',
                    },
                ],
            },
        ];

        const setInitialOpenGroups = () => {
            const activeGroup = navGroups.find((group) =>
                group.items.some((item) => isActive(item.route)),
            );

            openGroups.value = navGroups.reduce<Record<string, boolean>>(
                (acc, group) => {
                    acc[group.key] = activeGroup
                        ? group.key === activeGroup.key
                        : group.key === 'custom-konten';

                    return acc;
                },
                {},
            );
        };

        // Load sidebar state from localStorage.
        onMounted(() => {
            const saved = localStorage.getItem('sidebarCollapsed');
            const savedGroups = localStorage.getItem('adminOpenNavGroups');

            if (saved !== null) {
                sidebarCollapsed.value = JSON.parse(saved);
            }

            if (savedGroups !== null) {
                try {
                    const parsedGroups = JSON.parse(savedGroups) as Record<
                        string,
                        boolean
                    >;

                    openGroups.value = navGroups.reduce<
                        Record<string, boolean>
                    >((acc, group) => {
                        acc[group.key] = Boolean(parsedGroups[group.key]);

                        return acc;
                    }, {});
                } catch {
                    setInitialOpenGroups();
                }
            } else {
                setInitialOpenGroups();
            }
        });

        // Persist sidebar state on every change.
        watch(sidebarCollapsed, (newValue) => {
            localStorage.setItem('sidebarCollapsed', JSON.stringify(newValue));
        });

        watch(
            openGroups,
            (newValue) => {
                localStorage.setItem(
                    'adminOpenNavGroups',
                    JSON.stringify(newValue),
                );
            },
            { deep: true },
        );

        const isGroupActive = (group: AdminNavGroup) => {
            return group.items.some((item) => isActive(item.route));
        };

        const isGroupOpen = (groupKey: string) => {
            return Boolean(openGroups.value[groupKey]);
        };

        const toggleGroup = (groupKey: string) => {
            if (sidebarCollapsed.value) {
                sidebarCollapsed.value = false;

                return;
            }

            openGroups.value = {
                ...openGroups.value,
                [groupKey]: !openGroups.value[groupKey],
            };
        };

        const submenuStyle = (group: AdminNavGroup) => {
            const shouldShow =
                !sidebarCollapsed.value && isGroupOpen(group.key);

            return {
                maxHeight: shouldShow ? `${group.items.length * 36}px` : '0px',
                opacity: shouldShow ? 1 : 0,
            };
        };

        return {
            sidebarCollapsed,
            navGroups,
            isActive,
            isGroupActive,
            isGroupOpen,
            toggleGroup,
            submenuStyle,
        };
    },
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800;900&display=swap');
</style>
