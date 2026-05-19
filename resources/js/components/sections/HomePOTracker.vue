<template>
    <section
        id="po-tracker"
        class="pattern-grid bg-shironeri px-6 py-16 sm:py-24 lg:px-16"
    >
        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-12 flex flex-col md:flex-row md:items-end md:justify-between gap-6 border-b border-sumi/10 pb-8">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-matcha opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-matcha"></span>
                        </span>
                        <span class="text-[10px] font-bold tracking-widest text-matcha uppercase">LIVE PRE-ORDER TRACKER</span>
                    </div>
                    <h2 class="font-heading text-4xl font-black tracking-tight text-sumi uppercase">
                        PRE-ORDER<br />
                        <span class="text-matcha">AKTIF</span>
                    </h2>
                </div>
                
                <div class="flex items-center gap-4 rounded-2xl border border-matcha/30 bg-matcha/5 px-6 py-4 md:text-right">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-matcha/10 text-matcha">
                        <i class="bi bi-lightning-charge text-lg"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold tracking-widest text-hai uppercase block">TOTAL SLOT TERSEDIA</span>
                        <span class="text-2xl font-black text-matcha font-sans">{{ totalRemainingSlots }} SLOT</span>
                    </div>
                </div>
            </div>

            <!-- PO Table -->
            <div
                class="overflow-hidden rounded-3xl border border-sumi/5 bg-washi shadow-xl"
            >
                <!-- Table Header (Desktop Only) -->
                <div
                    class="hidden grid-cols-6 gap-6 bg-sumi/5 px-8 py-4 text-[10px] font-bold tracking-widest text-hai lg:grid border-b border-sumi/5"
                >
                    <span>PRODUK</span>
                    <span>BATCH</span>
                    <span>PROGRESS SLOT</span>
                    <span>SISA SLOT</span>
                    <span>TUTUP PO</span>
                    <span>STATUS</span>
                </div>

                <!-- Rows -->
                <div
                    v-for="po in poList"
                    :key="po.id"
                    class="group relative border-b border-sumi/5 bg-washi p-6 sm:p-8 transition-all hover:bg-sumi/[0.02]"
                >
                    <!-- Desktop View Layout (lg:grid) -->
                    <div class="hidden lg:grid lg:grid-cols-6 lg:gap-6 lg:items-center">
                        <!-- Product Info -->
                        <div class="space-y-1">
                            <h4 class="font-heading font-black text-sumi text-sm tracking-wide uppercase transition-colors group-hover:text-matcha">
                                {{ po.product }}
                            </h4>
                            <p class="text-[11px] text-hai font-sans">
                                SKU: <span class="font-mono text-sumi">{{ po.sku }}</span> · Size: <span class="font-bold text-sumi">{{ po.size }}</span>
                            </p>
                        </div>

                        <!-- Batch Label -->
                        <div class="text-sm font-bold text-sumi font-sans">
                            {{ po.batch }}
                        </div>

                        <!-- Progress Bar & Slots Info -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-[11px] font-sans">
                                <span class="text-hai">Slot Terisi</span>
                                <span class="font-bold text-sumi">{{ po.filled_slots }} / {{ po.max_slots }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-sumi/5 p-[1px]">
                                <div
                                    class="h-full rounded-full transition-all duration-1000 ease-out animate-pulse-soft"
                                    :class="po.progress > 90 ? 'bg-amber-500' : 'bg-matcha'"
                                    :style="`width: ${po.progress}%`"
                                ></div>
                            </div>
                            <p class="text-right text-[10px] font-bold text-hai">{{ po.progress }}%</p>
                        </div>

                        <!-- Remaining Slots -->
                        <div>
                            <span
                                :class="[
                                    po.remaining_slots <= 5 ? 'text-amber-600 bg-amber-50' : 'text-sumi bg-sumi/5',
                                    'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-black font-sans'
                                ]"
                            >
                                <i class="bi" :class="po.remaining_slots <= 5 ? 'bi-fire text-amber-600 animate-pulse' : 'bi-box-seam'"></i>
                                {{ po.remaining_slots }} Slot
                            </span>
                        </div>

                        <!-- Countdown Timer -->
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-1">
                                <div class="flex flex-col items-center">
                                    <span class="po-digit rounded-md bg-sumi px-2 py-1 text-xs font-mono font-black text-washi min-w-[28px] text-center">
                                        {{ String(po.timeLeft.h).padStart(2, '0') }}
                                    </span>
                                </div>
                                <span class="text-sumi font-black animate-pulse">:</span>
                                <div class="flex flex-col items-center">
                                    <span class="po-digit rounded-md bg-sumi px-2 py-1 text-xs font-mono font-black text-washi min-w-[28px] text-center">
                                        {{ String(po.timeLeft.m).padStart(2, '0') }}
                                    </span>
                                </div>
                                <span class="text-sumi font-black animate-pulse">:</span>
                                <div class="flex flex-col items-center">
                                    <span class="po-digit rounded-md bg-sumi px-2 py-1 text-xs font-mono font-black text-washi min-w-[28px] text-center">
                                        {{ String(po.timeLeft.s).padStart(2, '0') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            <span
                                :class="[
                                    po.status === 'BUKA'
                                        ? 'bg-matcha/10 text-matcha border-matcha/20'
                                        : po.status === 'HAMPIR HABIS'
                                          ? 'bg-amber-500/10 text-amber-600 border-amber-500/20'
                                          : 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-black tracking-widest uppercase'
                                ]"
                            >
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="po.status === 'BUKA' ? 'bg-matcha' : po.status === 'HAMPIR HABIS' ? 'bg-amber-500' : 'bg-rose-500'"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5" :class="po.status === 'BUKA' ? 'bg-matcha' : po.status === 'HAMPIR HABIS' ? 'bg-amber-500' : 'bg-rose-500'"></span>
                                </span>
                                {{ po.status }}
                            </span>
                        </div>
                    </div>

                    <!-- Mobile Responsive View Layout (lg:hidden) -->
                    <div class="flex flex-col space-y-5 lg:hidden">
                        <!-- Top header card row -->
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-matcha/10 px-2.5 py-1 text-[10px] font-black tracking-widest text-matcha uppercase border border-matcha/20">
                                {{ po.batch }}
                            </span>
                            <span
                                :class="[
                                    po.status === 'BUKA'
                                        ? 'bg-matcha/10 text-matcha border-matcha/20'
                                        : po.status === 'HAMPIR HABIS'
                                          ? 'bg-amber-500/10 text-amber-600 border-amber-500/20'
                                          : 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                    'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[9px] font-black tracking-widest uppercase'
                                ]"
                            >
                                <span class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="po.status === 'BUKA' ? 'bg-matcha' : po.status === 'HAMPIR HABIS' ? 'bg-amber-500' : 'bg-rose-500'"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5" :class="po.status === 'BUKA' ? 'bg-matcha' : po.status === 'HAMPIR HABIS' ? 'bg-amber-500' : 'bg-rose-500'"></span>
                                </span>
                                {{ po.status }}
                            </span>
                        </div>

                        <!-- Product Title -->
                        <div class="space-y-1">
                            <h4 class="font-heading font-black text-sumi text-base tracking-wide uppercase">
                                {{ po.product }}
                            </h4>
                            <p class="text-xs text-hai font-sans">
                                SKU: <span class="font-mono text-sumi font-semibold">{{ po.sku }}</span> · Size: <span class="font-bold text-sumi">{{ po.size }}</span>
                            </p>
                        </div>

                        <!-- Slots Progress -->
                        <div class="space-y-2 rounded-xl bg-sumi/[0.02] p-4 border border-sumi/5">
                            <div class="flex items-center justify-between text-xs font-sans">
                                <span class="text-hai">Slot Terisi</span>
                                <span class="font-bold text-sumi">{{ po.filled_slots }} / {{ po.max_slots }} <span class="text-[10px] text-hai font-normal">({{ po.progress }}%)</span></span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-sumi/5 p-[1px]">
                                <div
                                    class="h-full rounded-full transition-all duration-1000 ease-out"
                                    :class="po.progress > 90 ? 'bg-amber-500' : 'bg-matcha'"
                                    :style="`width: ${po.progress}%`"
                                ></div>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-xs text-hai flex items-center gap-1">
                                    <i class="bi bi-box-seam"></i>
                                    Sisa Slot
                                </span>
                                <span
                                    :class="po.remaining_slots <= 5 ? 'text-amber-600 font-black' : 'text-sumi font-bold'"
                                    class="text-xs"
                                >
                                    {{ po.remaining_slots }} Slot Tersedia
                                </span>
                            </div>
                        </div>

                        <!-- Countdown Clock Mobile -->
                        <div class="flex items-center justify-between pt-2 border-t border-sumi/5">
                            <span class="text-xs text-hai/80 tracking-wider uppercase font-bold flex items-center gap-1">
                                <i class="bi bi-clock-history"></i>
                                Tutup PO
                            </span>
                            <div class="flex items-center gap-1">
                                <span class="po-digit rounded-md bg-sumi px-2.5 py-1 text-xs font-mono font-black text-washi min-w-[28px] text-center">
                                    {{ String(po.timeLeft.h).padStart(2, '0') }}
                                </span>
                                <span class="text-sumi font-black animate-pulse">:</span>
                                <span class="po-digit rounded-md bg-sumi px-2.5 py-1 text-xs font-mono font-black text-washi min-w-[28px] text-center">
                                    {{ String(po.timeLeft.m).padStart(2, '0') }}
                                </span>
                                <span class="text-sumi font-black animate-pulse">:</span>
                                <span class="po-digit rounded-md bg-sumi px-2.5 py-1 text-xs font-mono font-black text-washi min-w-[28px] text-center">
                                    {{ String(po.timeLeft.s).padStart(2, '0') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-if="poList.length === 0"
                    class="px-8 py-16 text-center text-sm text-hai flex flex-col items-center justify-center gap-3"
                >
                    <i class="bi bi-calendar-x text-3xl text-hai/40"></i>
                    <span>Belum ada produk pre-order yang aktif untuk ditampilkan.</span>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Stat Card 1 -->
                <div class="relative overflow-hidden rounded-2xl border border-sumi/5 bg-washi p-6 sm:p-8 transition-all hover:-translate-y-1 hover:shadow-lg duration-300">
                    <div class="absolute top-0 left-0 h-full w-1.5 bg-sumi/30"></div>
                    <div class="flex items-center justify-between">
                        <div class="space-y-1 pl-2">
                            <span class="text-[10px] font-bold tracking-widest text-hai uppercase block">Total PO Aktif</span>
                            <span class="text-3xl sm:text-4xl font-heading font-black text-sumi">{{ totalPoAktif }}</span>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sumi/5 text-sumi">
                            <i class="bi bi-collection text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="relative overflow-hidden rounded-2xl border border-sumi/5 bg-washi p-6 sm:p-8 transition-all hover:-translate-y-1 hover:shadow-lg duration-300">
                    <div class="absolute top-0 left-0 h-full w-1.5 bg-matcha"></div>
                    <div class="flex items-center justify-between">
                        <div class="space-y-1 pl-2">
                            <span class="text-[10px] font-bold tracking-widest text-hai uppercase block">Slot Tersedia</span>
                            <span class="text-3xl sm:text-4xl font-heading font-black text-matcha">{{ totalRemainingSlots }}</span>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-matcha/10 text-matcha">
                            <i class="bi bi-box-seam text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="relative overflow-hidden rounded-2xl border border-sumi/5 bg-washi p-6 sm:p-8 transition-all hover:-translate-y-1 hover:shadow-lg duration-300">
                    <div class="absolute top-0 left-0 h-full w-1.5 bg-hai/40"></div>
                    <div class="flex items-center justify-between">
                        <div class="space-y-1 pl-2">
                            <span class="text-[10px] font-bold tracking-widest text-hai uppercase block">Batch Selesai</span>
                            <span class="text-3xl sm:text-4xl font-heading font-black text-hai">{{ completedBatchCount }}</span>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hai/10 text-hai">
                            <i class="bi bi-check2-circle text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';

import type { HomePreorderItem } from '@/types/home';

const props = defineProps<{
    preorderItems: HomePreorderItem[];
}>();

const nowTick = ref(Date.now());
let timerInterval: ReturnType<typeof setInterval> | undefined;

const calculateTimeLeft = (countdownEndsAt: string | null) => {
    if (!countdownEndsAt) return { h: 0, m: 0, s: 0 };
    const target = new Date(countdownEndsAt).getTime();
    if (isNaN(target)) return { h: 0, m: 0, s: 0 };
    const remaining = target - nowTick.value;
    if (remaining <= 0) return { h: 0, m: 0, s: 0 };
    return {
        h: Math.floor(remaining / 3600000),
        m: Math.floor((remaining % 3600000) / 60000),
        s: Math.floor((remaining % 60000) / 1000),
    };
};

const poList = computed(() => {
    return props.preorderItems.map((item) => ({
        ...item,
        timeLeft: calculateTimeLeft(item.countdown_ends_at),
    }));
});

const totalPoAktif = computed(() => poList.value.length);
const totalRemainingSlots = computed(() =>
    poList.value.reduce((sum, item) => sum + item.remaining_slots, 0),
);
const completedBatchCount = computed(
    () => poList.value.filter((item) => item.status === 'HABIS').length,
);

onMounted(() => {
    timerInterval = setInterval(() => {
        nowTick.value = Date.now();
    }, 1000);
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});
</script>

<style scoped>
.pattern-grid {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 50 50'%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='0.6' opacity='0.16' d='M0 0h50v50H0z M25 0v50 M0 25h50' /%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='0.4' opacity='0.09' d='M10 10 L40 40 M40 10 L10 40' /%3E%3C/svg%3E");
    background-size: 50px 50px;
}

.accent-left {
    border-left: 3px solid #7c8c5a;
    padding-left: 1rem;
}

@keyframes pulse-soft {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse-soft {
    animation: pulse-soft 2s ease-in-out infinite;
}
</style>
