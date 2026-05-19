<template>
    <section id="galeri" class="pattern-plum bg-washi px-4 py-20 sm:px-6 md:py-28 lg:px-16 overflow-hidden relative">
        <!-- Decorative glowing ambient light circles -->
        <div class="absolute top-0 left-0 w-80 h-80 bg-radial from-matcha/5 to-transparent pointer-events-none rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-radial from-sakura/5 to-transparent pointer-events-none rounded-full blur-3xl"></div>

        <div class="mx-auto max-w-7xl relative z-10">
            <!-- Header with Japanese stamp badging -->
            <div class="mb-16 md:mb-24 flex flex-col items-center text-center">
                <div class="inline-flex items-center gap-2 mb-4 px-3.5 py-1 rounded-full border border-matcha/20 bg-matcha/5 shadow-xs">
                    <span class="font-heading text-[9px] sm:text-[10px] font-bold tracking-[0.2em] text-matcha uppercase">
                        カスタムギャラリー
                    </span>
                    <span class="h-1.5 w-1.5 rounded-full bg-matcha animate-pulse"></span>
                    <span class="font-heading text-[9px] sm:text-[10px] font-bold tracking-[0.2em] text-matcha uppercase">
                        COMMUNITY PORTFOLIO
                    </span>
                </div>
                
                <h2 class="font-heading text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-sumi uppercase relative inline-block">
                    Galeri Karya Komunitas
                    <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 h-1 w-24 bg-matcha rounded-full shadow-xs"></span>
                </h2>
                
                <p class="mt-6 max-w-xl text-xs sm:text-sm md:text-base leading-relaxed text-hai font-sans">
                    Koleksi karya custom, konsep modifikasi, dan kolaborasi streetwear terpopuler dari kreator komunitas Bogor Sneakers.
                </p>
            </div>

            <!-- Pinterest-style 2-Column Responsive Masonry Grid -->
            <div
                class="columns-2 gap-3 [column-fill:balance] sm:columns-2 md:columns-3 xl:columns-4 sm:gap-4"
            >
                <div
                    v-for="(gallery, idx) in galleryItems"
                    :key="gallery.id"
                    class="mb-3 sm:mb-4 break-inside-avoid group cursor-zoom-in"
                    @click="openLightbox(idx)"
                >
                    <div
                        class="card-lift overflow-hidden rounded-2xl sm:rounded-3xl border border-sumi/5 bg-shironeri transition-all duration-300 hover:border-sumi/10 hover:shadow-lg hover:-translate-y-1"
                    >
                        <!-- Image Container with Hover Effects -->
                        <div class="img-reveal bg-sumi/5 relative overflow-hidden">
                            <!-- Image Display -->
                            <img
                                v-if="gallery.image_url"
                                :src="gallery.image_url"
                                :alt="`Galeri karya slot ${gallery.slot}`"
                                class="block h-auto w-full object-contain transition-transform duration-700 group-hover:scale-105"
                                loading="lazy"
                                decoding="async"
                            />
                            
                            <!-- Premium Sneaker Blueprint Empty State (when image_url is null) -->
                            <div
                                v-else
                                class="relative flex min-h-[180px] sm:min-h-[240px] w-full items-center justify-center bg-radial from-shironeri via-washi to-washi/30 overflow-hidden"
                            >
                                <!-- Circular Grid Blueprint Background -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-[0.06] group-hover:opacity-[0.1] transition-opacity duration-500">
                                    <div class="h-44 w-44 rounded-full border border-sumi animate-spin-slow"></div>
                                    <div class="absolute h-32 w-32 rounded-full border border-dashed border-sumi"></div>
                                    <div class="absolute h-18 w-18 rounded-full border border-sumi"></div>
                                    <div class="absolute h-full w-[1px] bg-sumi"></div>
                                    <div class="absolute w-full h-[1px] bg-sumi"></div>
                                </div>

                                <!-- SVG Repo Color Sneaker SVG -->
                                <svg viewBox="0 -588.5 2201 2201" class="h-20 w-20 sm:h-28 sm:w-28 relative z-10 transition-transform duration-500 transform group-hover:scale-105" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M36.493482 977.39459h2117.908116l-7.474453-377.240017s-186.861314-311.288965-476.166595-366.248175S769.429507 39.570631 769.429507 39.570631s-175.869472 455.941606-696.443109 305.133534l-36.492916 18.466294v615.543152z" fill="#FFFFFF" />
                                    <path d="M51.002714 608.508373a481.003006 481.003006 0 0 1 197.853156-40.449979c93.21082 7.034779 175.869472-113.435809 190.378703-190.378703 0 0-355.256333-10.991842-366.248175-32.975526s-36.492915 18.466295-36.492916 18.466294z" fill="#EA5D5C" />
                                    <path d="M1025.319588 321.40146l453.303564 115.634178-258.08845 389.990553-489.356806-19.345641 294.141692-486.27909z" fill="#4C66AF" />
                                    <path d="M1755.177896 226.871619L783.938738 1.319021h-10.112495a43.967368 43.967368 0 0 0-38.691283 23.742379c-57.597252 116.513525-175.869472 254.571061-315.685702 287.98626a622.138257 622.138257 0 0 1-143.33362 16.267926 579.050236 579.050236 0 0 1-215.000429-38.691284 43.967368 43.967368 0 0 0-17.147274-3.517389 43.967368 43.967368 0 0 0-43.967368 43.967368v692.925719h2198.368399v-293.262344c0.439674-241.820524-209.284672-450.225848-443.19107-503.866037zM2110.873903 729.418635v31.216831h-236.984113a842.854444 842.854444 0 0 0-175.869472-456.38128l38.691284 7.914127c182.904251 42.648347 374.162301 211.043366 374.162301 417.250322zM1209.103186 405.818806l-233.906397 354.81666h-175.869472l257.209102-389.55088z m91.452126 21.104337l113.435809 25.940747-203.12924 307.771576h-131.902104z m204.887934 47.045084l219.83684 50.562473a754.919708 754.919708 0 0 1 57.597252 236.104766H1318.142259zM794.93058 94.529841l61.554315 14.509232-7.034779 31.216831a43.967368 43.967368 0 1 0 85.736368 19.785315l10.112494-32.535852 85.736368 19.785316-7.034779 30.337484a43.967368 43.967368 0 0 0 85.736368 19.785315l7.034778-30.337484 85.736368 19.785316-7.034779 29.89781a43.967368 43.967368 0 0 0 85.736368 19.785316l7.034779-29.897811 85.736367 19.785316-6.595105 29.458137a43.967368 43.967368 0 1 0 85.736367 19.785315l6.595106-29.458136 92.771146 21.54401a766.351224 766.351224 0 0 1 123.987978 152.566767l-952.772864-219.83684a776.024045 776.024045 0 0 0 73.425504-106.401031zM651.157287 277.873766l314.366681 72.106483-270.838987 410.655217H88.374977v-83.537999c31.656505-52.321168 63.752684-59.355947 143.773293-76.063547 31.216831-6.595105 66.830399-14.069558 109.91842-25.940747a199.172177 199.172177 0 0 0 134.97982-98.047231 156.52383 156.52383 0 0 0 12.750536-95.848862 515.737226 515.737226 0 0 0 163.118935-103.323314zM273.91727 415.931301a714.030056 714.030056 0 0 0 127.94504-12.310863 67.270073 67.270073 0 0 1-6.595105 35.613568 118.711894 118.711894 0 0 1-79.141262 51.002147c-40.010305 10.991842-74.304852 18.466295-104.202662 24.621726a416.810648 416.810648 0 0 0-125.746673 40.010305V391.309575a708.753972 708.753972 0 0 0 189.93903 24.621726zM86.176608 934.306569v-87.934736h2022.498927v87.934736h-2022.498927z" fill="#3F4651" />
                                </svg>
                                
                                <!-- Blueprint watermark tag -->
                                <span class="absolute bottom-2 right-3 text-[6px] sm:text-[7px] font-heading font-bold uppercase tracking-widest text-hai/25 pointer-events-none">
                                    Slot #{{ gallery.slot }} // Custom Concept
                                </span>
                            </div>

                            <!-- Hover Overlay with Magnifying Zoom Glass Badge -->
                            <div class="absolute inset-0 bg-sumi/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center z-20">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-washi text-sumi shadow-md transform scale-75 group-hover:scale-100 transition-transform duration-300">
                                    <i class="bi bi-zoom-in text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Details -->
                        <div class="p-3 sm:p-4 bg-white/40 backdrop-blur-xs">
                            <p class="text-xs sm:text-sm font-extrabold text-sumi group-hover:text-matcha transition-colors line-clamp-1">
                                {{ gallery.title }}
                            </p>
                            <p class="text-[10px] sm:text-xs text-hai flex items-center gap-1 mt-0.5 font-sans">
                                <i class="bi bi-person-circle text-[9px] sm:text-[10px] opacity-75"></i>
                                <span class="font-medium truncate">{{ gallery.author }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal (Premium Fullscreen Viewer) -->
        <Transition name="fade">
            <div
                v-if="activeIndex !== null"
                class="fixed inset-0 z-50 flex items-center justify-center bg-sumi/95 p-4 sm:p-6 backdrop-blur-md"
                @click.self="activeIndex = null"
            >
                <!-- Close Button -->
                <button
                    @click="activeIndex = null"
                    class="absolute top-6 right-6 z-55 flex h-10 w-10 items-center justify-center rounded-full border border-washi/10 bg-washi/5 text-washi transition-all duration-300 hover:bg-washi/25 active:scale-95"
                    aria-label="Close Lightbox"
                >
                    <i class="bi bi-x-lg text-lg"></i>
                </button>

                <!-- Previous Button -->
                <button
                    @click="prevSlide"
                    class="absolute left-4 z-55 flex h-12 w-12 items-center justify-center rounded-full border border-washi/10 bg-washi/5 text-washi transition-all duration-300 hover:bg-washi/25 active:scale-90"
                    aria-label="Previous Image"
                >
                    <i class="bi bi-chevron-left text-xl"></i>
                </button>

                <!-- Main Display Content -->
                <div class="flex max-w-4xl w-full flex-col items-center justify-center gap-6" @click.self="activeIndex = null">
                    <div class="relative max-h-[70vh] w-full flex items-center justify-center rounded-3xl overflow-hidden shadow-2xl bg-washi/2 flex-1">
                        <!-- Large Image -->
                        <img
                            v-if="galleryItems[activeIndex]?.image_url"
                            :src="galleryItems[activeIndex].image_url"
                            :alt="`Large Galeri karya slot ${galleryItems[activeIndex].slot}`"
                            class="max-h-[70vh] max-w-full object-contain rounded-2xl animate-scale-up"
                        />
                        
                        <!-- Blueprint empty state shown in Lightbox! -->
                        <div
                            v-else
                            class="relative flex h-[40vh] sm:h-[50vh] w-full items-center justify-center bg-radial from-sumi/60 to-sumi/20 rounded-2xl border border-washi/5 overflow-hidden animate-scale-up"
                        >
                            <!-- Rotating blueprint rings -->
                            <div class="absolute inset-0 flex items-center justify-center opacity-[0.05] pointer-events-none">
                                <div class="h-64 w-64 rounded-full border border-washi animate-spin-slow"></div>
                                <div class="absolute h-48 w-48 rounded-full border border-dashed border-washi"></div>
                                <div class="absolute h-32 w-32 rounded-full border border-washi"></div>
                            </div>
                            
                            <!-- Large SVG Repo Color Sneaker SVG -->
                            <svg viewBox="0 -588.5 2201 2201" class="h-32 w-32 sm:h-48 sm:w-48 animate-pulse-slow relative z-10" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                <path d="M36.493482 977.39459h2117.908116l-7.474453-377.240017s-186.861314-311.288965-476.166595-366.248175S769.429507 39.570631 769.429507 39.570631s-175.869472 455.941606-696.443109 305.133534l-36.492916 18.466294v615.543152z" fill="#FFFFFF" />
                                <path d="M51.002714 608.508373a481.003006 481.003006 0 0 1 197.853156-40.449979c93.21082 7.034779 175.869472-113.435809 190.378703-190.378703 0 0-355.256333-10.991842-366.248175-32.975526s-36.492915 18.466295-36.492916 18.466294z" fill="#EA5D5C" />
                                <path d="M1025.319588 321.40146l453.303564 115.634178-258.08845 389.990553-489.356806-19.345641 294.141692-486.27909z" fill="#4C66AF" />
                                <path d="M1755.177896 226.871619L783.938738 1.319021h-10.112495a43.967368 43.967368 0 0 0-38.691283 23.742379c-57.597252 116.513525-175.869472 254.571061-315.685702 287.98626a622.138257 622.138257 0 0 1-143.33362 16.267926 579.050236 579.050236 0 0 1-215.000429-38.691284 43.967368 43.967368 0 0 0-17.147274-3.517389 43.967368 43.967368 0 0 0-43.967368 43.967368v692.925719h2198.368399v-293.262344c0.439674-241.820524-209.284672-450.225848-443.19107-503.866037zM2110.873903 729.418635v31.216831h-236.984113a842.854444 842.854444 0 0 0-175.869472-456.38128l38.691284 7.914127c182.904251 42.648347 374.162301 211.043366 374.162301 417.250322zM1209.103186 405.818806l-233.906397 354.81666h-175.869472l257.209102-389.55088z m91.452126 21.104337l113.435809 25.940747-203.12924 307.771576h-131.902104z m204.887934 47.045084l219.83684 50.562473a754.919708 754.919708 0 0 1 57.597252 236.104766H1318.142259zM794.93058 94.529841l61.554315 14.509232-7.034779 31.216831a43.967368 43.967368 0 1 0 85.736368 19.785315l10.112494-32.535852 85.736368 19.785316-7.034779 30.337484a43.967368 43.967368 0 0 0 85.736368 19.785315l7.034778-30.337484 85.736368 19.785316-7.034779 29.89781a43.967368 43.967368 0 0 0 85.736368 19.785316l7.034779-29.897811 85.736367 19.785316-6.595105 29.458137a43.967368 43.967368 0 1 0 85.736367 19.785315l6.595106-29.458136 92.771146 21.54401a766.351224 766.351224 0 0 1 123.987978 152.566767l-952.772864-219.83684a776.024045 776.024045 0 0 0 73.425504-106.401031zM651.157287 277.873766l314.366681 72.106483-270.838987 410.655217H88.374977v-83.537999c31.656505-52.321168 63.752684-59.355947 143.773293-76.063547 31.216831-6.595105 66.830399-14.069558 109.91842-25.940747a199.172177 199.172177 0 0 0 134.97982-98.047231 156.52383 156.52383 0 0 0 12.750536-95.848862 515.737226 515.737226 0 0 0 163.118935-103.323314zM273.91727 415.931301a714.030056 714.030056 0 0 0 127.94504-12.310863 67.270073 67.270073 0 0 1-6.595105 35.613568 118.711894 118.711894 0 0 1-79.141262 51.002147c-40.010305 10.991842-74.304852 18.466295-104.202662 24.621726a416.810648 416.810648 0 0 0-125.746673 40.010305V391.309575a708.753972 708.753972 0 0 0 189.93903 24.621726zM86.176608 934.306569v-87.934736h2022.498927v87.934736h-2022.498927z" fill="#3F4651" />
                            </svg>
                            
                            <span class="absolute bottom-6 text-[10px] font-heading font-bold uppercase tracking-widest text-washi/20">
                                Design Slot #{{ galleryItems[activeIndex]?.slot }} // Draft Concept
                            </span>
                        </div>
                    </div>

                    <!-- Footer Details -->
                    <div class="text-center text-washi max-w-xl px-4 animate-slide-up">
                        <span class="inline-block text-[9px] font-heading font-extrabold uppercase tracking-widest bg-matcha px-3 py-1 rounded-full mb-3 text-sumi shadow-xs">
                            Karya {{ galleryItems[activeIndex]?.slot }} dari {{ galleryItems.length }}
                        </span>
                        
                        <h3 class="text-xl sm:text-2xl font-heading font-black tracking-tight uppercase mb-1">
                            {{ galleryItems[activeIndex]?.title }}
                        </h3>
                        
                        <p class="text-xs sm:text-sm text-washi/60 font-sans">
                            Konsep oleh: <span class="font-bold text-matcha">{{ galleryItems[activeIndex]?.author }}</span>
                        </p>
                    </div>
                </div>

                <!-- Next Button -->
                <button
                    @click="nextSlide"
                    class="absolute right-4 z-55 flex h-12 w-12 items-center justify-center rounded-full border border-washi/10 bg-washi/5 text-washi transition-all duration-300 hover:bg-washi/25 active:scale-90"
                    aria-label="Next Image"
                >
                    <i class="bi bi-chevron-right text-xl"></i>
                </button>
            </div>
        </Transition>
    </section>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';

type GallerySlotApi = {
    id: number;
    slot: number;
    image_url: string | null;
    title?: string | null;
    author?: string | null;
};

type GalleryItem = {
    id: number;
    slot: number;
    image_url: string | null;
    title: string;
    author: string;
};

const props = defineProps<{
    gallerySlots: GallerySlotApi[];
}>();

// Lightbox interactive state
const activeIndex = ref<number | null>(null);

const openLightbox = (index: number) => {
    activeIndex.value = index;
};

const prevSlide = () => {
    if (activeIndex.value === null) return;
    activeIndex.value = (activeIndex.value - 1 + galleryItems.value.length) % galleryItems.value.length;
};

const nextSlide = () => {
    if (activeIndex.value === null) return;
    activeIndex.value = (activeIndex.value + 1) % galleryItems.value.length;
};

// Keyboard navigation support for Lightbox
const handleKeyDown = (e: KeyboardEvent) => {
    if (activeIndex.value === null) return;
    if (e.key === 'Escape') activeIndex.value = null;
    if (e.key === 'ArrowLeft') prevSlide();
    if (e.key === 'ArrowRight') nextSlide();
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});

const galleryDefaultTitles: Record<number, string> = {
    1: 'Air Max 97 x Jogja Streets',
    2: 'Samba OG - Bogor Vibe',
    3: 'Jordan 1 Bred - Cold Day',
    4: 'NB 574 Navy x Rain',
    5: 'Vans Old Skool',
    6: 'Converse Chuck 70',
    7: 'Asics Gel-Kayano',
    8: 'Puma RS-X Effekt',
};

const galleryDefaultAuthor = '@bogorsneakers';

const getDefaultGalleryTitle = (slot: number) => {
    return galleryDefaultTitles[slot] ?? `Galeri Karya Slot ${slot}`;
};

const normalizeGalleryAuthor = (author?: string | null) => {
    const trimmed = (author ?? '').trim();
    if (trimmed === '') return galleryDefaultAuthor;
    if (trimmed.startsWith('@')) return trimmed;
    return `@${trimmed}`;
};

const buildGalleryPlaceholders = (): GalleryItem[] => {
    return Array.from({ length: 8 }, (_, index) => {
        const slot = index + 1;
        return {
            id: slot,
            slot,
            image_url: null,
            title: getDefaultGalleryTitle(slot),
            author: galleryDefaultAuthor,
        };
    });
};

const galleryItems = computed<GalleryItem[]>(() => {
    const placeholders = buildGalleryPlaceholders();
    const normalizedSlots = [...props.gallerySlots]
        .sort((a, b) => a.slot - b.slot)
        .slice(0, 8)
        .map((slot) => ({
            id: slot.id,
            slot: slot.slot,
            image_url: slot.image_url,
            title:
                (slot.title ?? '').trim() || getDefaultGalleryTitle(slot.slot),
            author: normalizeGalleryAuthor(slot.author),
        }));

    return placeholders.map((placeholder) => {
        return (
            normalizedSlots.find((slot) => slot.slot === placeholder.slot) ??
            placeholder
        );
    });
});
</script>

<style scoped>
.pattern-plum {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Ccircle cx='25' cy='25' r='3' fill='%234a4a4a' opacity='0.1'/%3E%3Ccircle cx='75' cy='25' r='3' fill='%234a4a4a' opacity='0.1'/%3E%3Ccircle cx='25' cy='75' r='3' fill='%234a4a4a' opacity='0.1'/%3E%3Ccircle cx='75' cy='75' r='3' fill='%234a4a4a' opacity='0.1'/%3E%3Ccircle cx='50' cy='50' r='2' fill='%234a4a4a' opacity='0.13'/%3E%3Cpath fill='none' stroke='%234a4a4a' stroke-width='0.5' opacity='0.08' d='M25 25 L75 75 M75 25 L25 75'/%3E%3C/svg%3E");
    background-size: 100px 100px;
}

.card-lift {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow:
        0 2px 8px rgba(26, 26, 26, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.card-lift:hover {
    transform: translateY(-4px);
    box-shadow:
        0 12px 24px rgba(26, 26, 26, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.img-reveal {
    position: relative;
    overflow: hidden;
}

.img-reveal::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        rgba(26, 26, 26, 0.03) 0%,
        transparent 50%
    );
    pointer-events: none;
}

/* Lightbox Animations */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.4s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@keyframes scaleUp {
    from {
        transform: scale(0.95);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-scale-up {
    animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes spinSlow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin-slow {
    animation: spinSlow 30s linear infinite;
}

@keyframes pulseSlow {
    0%, 100% {
        opacity: 0.3;
    }
    50% {
        opacity: 0.55;
    }
}

.animate-pulse-slow {
    animation: pulseSlow 3s ease-in-out infinite;
}
</style>
