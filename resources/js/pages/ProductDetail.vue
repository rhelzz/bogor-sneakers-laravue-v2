<template>
    <div class="font-body min-h-screen bg-washi text-sumi antialiased">
        <Head :title="product ? product.name : 'Detail Produk'" />
        <FloatingMenuNav current-page="katalog" />
        <FloatingAdminPanel :contacts="contacts" />
        <FloatingOrderPanel :orders="orders" />

        <section
            class="min-h-screen bg-[radial-gradient(circle_at_1px_1px,rgba(26,26,26,0.06)_1px,transparent_0)] bg-size-[24px_24px] px-4 pt-28 pb-14 sm:px-6 lg:px-10"
        >
            <div class="mx-auto max-w-6xl">
                <Link
                    href="/katalog"
                    class="inline-flex items-center gap-2 rounded-full border border-sumi/20 bg-shironeri px-4 py-2 text-xs tracking-widest text-usuzumi uppercase transition hover:border-sumi/35 hover:text-sumi"
                >
                    <i class="bi bi-arrow-left" />
                    Kembali ke katalog
                </Link>

                <div
                    v-if="product"
                    class="mt-5 overflow-hidden rounded-[28px] border border-sumi/10 bg-shironeri/95 shadow-[0_16px_36px_rgba(26,26,26,0.08)]"
                >
                    <div class="grid gap-0 lg:grid-cols-[1.05fr_0.95fr]">
                        <div
                            class="border-b border-sumi/10 p-5 lg:border-r lg:border-b-0 lg:p-7"
                        >
                            <div
                                class="group relative aspect-square overflow-hidden rounded-3xl border border-sumi/10 bg-sumi/5"
                                @mouseenter="onGalleryEnter"
                                @mouseleave="onGalleryLeave"
                                @mousemove="onGalleryMouseMove"
                            >
                                <template v-if="galleryImages.length > 0">
                                    <div class="absolute inset-0">
                                        <div
                                            v-for="(
                                                image, idx
                                            ) in galleryImages"
                                            :key="image.id"
                                            class="absolute inset-0 transition-all duration-700 ease-in-out"
                                            :class="
                                                idx === currentImageIndex
                                                    ? 'scale-100 opacity-100'
                                                    : 'pointer-events-none scale-105 opacity-0'
                                            "
                                        >
                                            <img
                                                :src="image.image_url"
                                                :alt="`${product.name} - gambar ${idx + 1}`"
                                                class="h-full w-full object-cover transition-transform duration-200"
                                                :style="imageStyle(idx)"
                                                loading="lazy"
                                            />
                                        </div>
                                    </div>

                                    <button
                                        v-if="galleryImages.length > 1"
                                        class="absolute top-1/2 left-3 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-sumi/35 text-washi transition md:opacity-0 md:group-hover:opacity-100"
                                        @click="prevImage"
                                    >
                                        <i class="bi bi-chevron-left" />
                                    </button>
                                    <button
                                        v-if="galleryImages.length > 1"
                                        class="absolute top-1/2 right-3 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-sumi/35 text-washi transition md:opacity-0 md:group-hover:opacity-100"
                                        @click="nextImage"
                                    >
                                        <i class="bi bi-chevron-right" />
                                    </button>

                                    <div
                                        v-if="galleryImages.length > 1"
                                        class="absolute inset-x-0 bottom-3 z-20 flex justify-center gap-2"
                                    >
                                        <button
                                            v-for="(
                                                image, idx
                                            ) in galleryImages"
                                            :key="`dot-${image.id}`"
                                            class="transition-all"
                                            :class="
                                                idx === currentImageIndex
                                                    ? 'h-2.5 w-6 rounded-full bg-washi'
                                                    : 'h-2.5 w-2.5 rounded-full bg-washi/45'
                                            "
                                            @click="goToImage(idx)"
                                        />
                                    </div>
                                </template>

                                <div
                                    v-else
                                    class="absolute inset-0 flex items-center justify-center text-hai/35"
                                >
                                    <i class="bi bi-image text-6xl" />
                                </div>
                                <span
                                    class="absolute top-4 left-4 rounded-md px-3 py-1 text-[11px] tracking-[0.08em] uppercase"
                                    :class="statusBadgeClass(product.status)"
                                >
                                    {{ statusText(product.status) }}
                                </span>
                            </div>

                            <div
                                v-if="galleryImages.length > 1"
                                class="mt-3 grid grid-cols-5 gap-2"
                            >
                                <button
                                    v-for="(image, idx) in galleryImages"
                                    :key="`thumb-${image.id}`"
                                    class="overflow-hidden rounded-lg border transition"
                                    :class="
                                        idx === currentImageIndex
                                            ? 'border-sumi'
                                            : 'border-sumi/15 hover:border-matcha'
                                    "
                                    @click="goToImage(idx)"
                                >
                                    <img
                                        :src="image.image_url"
                                        :alt="`Thumbnail ${idx + 1}`"
                                        class="aspect-square h-full w-full object-cover"
                                        loading="lazy"
                                    />
                                </button>
                            </div>

                            <div
                                class="mt-4 grid grid-cols-1 gap-2 text-xs text-hai sm:grid-cols-2"
                            >
                                <div
                                    class="rounded-xl border border-sumi/10 bg-washi p-3"
                                >
                                    <p class="mb-1 tracking-[0.08em] uppercase">
                                        Koleksi
                                    </p>
                                    <p class="font-semibold text-sumi">
                                        {{ product.collectionLabel }}
                                    </p>
                                </div>
                                <div
                                    v-if="product.shoe_model_name"
                                    class="rounded-xl border border-sumi/10 bg-washi p-3"
                                >
                                    <p class="mb-1 tracking-[0.08em] uppercase">
                                        Model
                                    </p>
                                    <p class="font-semibold text-sumi">
                                        {{ product.shoe_model_name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 lg:p-7">
                            <p
                                class="text-[11px] tracking-[0.14em] text-hai uppercase"
                            >
                                {{ product.collectionLabel }}
                            </p>
                            <h1
                                class="mt-2 text-3xl leading-tight font-bold tracking-tight"
                            >
                                {{ product.name }}
                            </h1>
                            <p class="mt-3 text-2xl font-bold text-sumi">
                                {{ formatCurrency(product.price) }}
                            </p>
                            <p class="mt-1 text-xs text-hai">
                                {{ priceSubtext(product.status) }}
                            </p>

                            <div class="mt-6">
                                <p
                                    class="mb-2 text-[11px] tracking-[0.12em] text-hai uppercase"
                                >
                                    Pilih Ukuran
                                </p>
                                <div
                                    class="grid grid-cols-4 gap-2 sm:grid-cols-5"
                                >
                                    <button
                                        v-for="size in availableSizes"
                                        :key="size"
                                        class="rounded-md border px-2 py-2 text-xs transition"
                                        :class="sizeButtonClass(size)"
                                        @click="selectSize(size)"
                                    >
                                        {{ size }}
                                    </button>
                                </div>
                            </div>

                            <div
                                class="mt-6 rounded-2xl border border-sumi/10 bg-washi p-4"
                            >
                                <p class="text-xs leading-relaxed text-usuzumi">
                                    {{
                                        product.description &&
                                        product.description.trim().length > 0
                                            ? product.description
                                            : 'Belum ada deskripsi untuk produk ini.'
                                    }}
                                </p>
                            </div>

                            <div class="mt-6 flex flex-wrap gap-2">
                                <button
                                    class="rounded-full px-5 py-2.5 text-xs tracking-widest uppercase transition"
                                    :class="ctaClass"
                                    :disabled="product.status === 'habis'"
                                >
                                    {{ ctaText }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="mt-5 rounded-3xl border border-dashed border-sumi/20 bg-shironeri p-10 text-center"
                >
                    <div
                        class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl border border-sumi/15 text-hai"
                    >
                        <i class="bi bi-search" />
                    </div>
                    <h2 class="text-xl font-bold">Produk tidak ditemukan</h2>
                    <p class="mt-2 text-sm text-hai">
                        Produk ini tidak tersedia atau sudah tidak aktif.
                    </p>
                    <Link
                        href="/katalog"
                        class="mt-5 inline-flex items-center gap-2 rounded-full bg-sumi px-5 py-2.5 text-xs tracking-widest text-washi uppercase transition hover:opacity-85"
                    >
                        Kembali ke katalog
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue';
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue';
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue';
import type { CatalogDetailItem, CatalogStatus } from '@/types/catalog';
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui';

const props = defineProps<{
    product: CatalogDetailItem | null;
}>();
const page = usePage();

const product = computed(() => props.product);
const availableSizes = computed(() => product.value?.sizes ?? []);

const contacts = computed<FloatingContact[]>(() => {
    const sharedContacts = page.props.floatingContacts;

    if (!Array.isArray(sharedContacts)) {
        return [];
    }

    return sharedContacts as FloatingContact[];
});

const orders = ref<FloatingOrder[]>([
    {
        id: '#BGS-2841',
        product: 'Nike Air Max 97 Silver',
        size: '42',
        status: 'Produksi',
        statusClass:
            'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700',
        progress: 55,
        progressClass: 'bg-sumi',
    },
    {
        id: '#BGS-2790',
        product: 'Adidas Samba OG White',
        size: '40',
        status: 'Dikirim',
        statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700',
        progress: 85,
        progressClass: 'bg-sumi',
    },
]);

const galleryImages = computed(() => product.value?.images ?? []);

const selectedSize = ref<number | null>(null);
const currentImageIndex = ref(0);
const isGalleryHovered = ref(false);
const supportsHover = ref(false);
const zoomOrigin = ref({ x: 50, y: 50 });

let galleryInterval: ReturnType<typeof setInterval> | undefined;

watch(
    product,
    (value) => {
        selectedSize.value = value?.sizes[0] ?? null;
        currentImageIndex.value = 0;
    },
    { immediate: true },
);

watch(
    galleryImages,
    () => {
        currentImageIndex.value = 0;
        restartAutoplay();
    },
    { deep: true },
);

const nextImage = () => {
    if (galleryImages.value.length === 0) {
        return;
    }

    currentImageIndex.value =
        (currentImageIndex.value + 1) % galleryImages.value.length;
};

const prevImage = () => {
    if (galleryImages.value.length === 0) {
        return;
    }

    currentImageIndex.value =
        (currentImageIndex.value - 1 + galleryImages.value.length) %
        galleryImages.value.length;
};

const goToImage = (index: number) => {
    if (index < 0 || index >= galleryImages.value.length) {
        return;
    }

    currentImageIndex.value = index;
};

const stopAutoplay = () => {
    if (galleryInterval !== undefined) {
        clearInterval(galleryInterval);
        galleryInterval = undefined;
    }
};

const startAutoplay = () => {
    stopAutoplay();

    if (galleryImages.value.length <= 1) {
        return;
    }

    if (supportsHover.value && isGalleryHovered.value) {
        return;
    }

    galleryInterval = setInterval(nextImage, 3800);
};

const restartAutoplay = () => {
    startAutoplay();
};

const onGalleryEnter = () => {
    if (!supportsHover.value) {
        return;
    }

    isGalleryHovered.value = true;
    stopAutoplay();
};

const onGalleryLeave = () => {
    if (!supportsHover.value) {
        return;
    }

    isGalleryHovered.value = false;
    zoomOrigin.value = { x: 50, y: 50 };
    startAutoplay();
};

const onGalleryMouseMove = (event: MouseEvent) => {
    if (!supportsHover.value || !isGalleryHovered.value) {
        return;
    }

    const target = event.currentTarget as HTMLElement;
    const rect = target.getBoundingClientRect();

    if (rect.width <= 0 || rect.height <= 0) {
        return;
    }

    const x = ((event.clientX - rect.left) / rect.width) * 100;
    const y = ((event.clientY - rect.top) / rect.height) * 100;

    zoomOrigin.value = {
        x: Math.min(Math.max(x, 0), 100),
        y: Math.min(Math.max(y, 0), 100),
    };
};

const imageStyle = (index: number) => {
    if (
        !supportsHover.value ||
        !isGalleryHovered.value ||
        index !== currentImageIndex.value
    ) {
        return {};
    }

    return {
        transform: 'scale(1.08)',
        transformOrigin: `${zoomOrigin.value.x}% ${zoomOrigin.value.y}%`,
    };
};

onMounted(() => {
    supportsHover.value = window.matchMedia(
        '(hover: hover) and (pointer: fine)',
    ).matches;
    startAutoplay();
});

onUnmounted(() => {
    stopAutoplay();
});

const ctaText = computed(() => {
    if (product.value?.status === 'habis') {
        return 'Notifikasi Saya';
    }

    if (product.value?.status === 'po') {
        return 'Pre-Order Sekarang';
    }

    return 'Pesan Sekarang';
});

const ctaClass = computed(() => {
    if (product.value?.status === 'habis') {
        return 'cursor-not-allowed border border-sumi/20 bg-sumi/5 text-hai';
    }

    return 'bg-sumi text-washi hover:opacity-85';
});

const selectSize = (size: number) => {
    if (!product.value?.sizes.includes(size)) {
        return;
    }

    selectedSize.value = size;
};

const statusText = (status: CatalogStatus) => {
    if (status === 'po') {
        return 'PO';
    }

    if (status === 'ready') {
        return 'Ready';
    }

    return 'Habis';
};

const statusBadgeClass = (status: CatalogStatus) => {
    if (status === 'po' || status === 'ready') {
        return 'bg-matcha text-washi shadow-sm';
    }

    return 'bg-sumi text-washi shadow-sm';
};

const sizeButtonClass = (size: number) => {
    if (!product.value?.sizes.includes(size)) {
        return 'cursor-not-allowed border-sumi/10 bg-sumi/5 text-hai/50';
    }

    if (selectedSize.value === size) {
        return 'border-sumi bg-sumi text-washi';
    }

    return 'border-sumi/15 bg-shironeri text-usuzumi hover:border-matcha hover:text-sumi';
};

const priceSubtext = (status: CatalogStatus) => {
    if (status === 'po') {
        const min = product.value?.preorder_min_days ?? 14;
        const max = product.value?.preorder_max_days ?? 21;

        return `Pre-order · estimasi ${min}-${max} hari`;
    }

    if (status === 'ready') {
        return 'Stok tersedia · siap kirim';
    }

    return 'Stok habis · aktifkan notifikasi';
};

const formatCurrency = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(price);
};
</script>
