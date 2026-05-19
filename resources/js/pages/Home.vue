<template>
    <div class="font-body bg-washi text-sumi antialiased">
        <Head title="Home" />
        <FloatingMenuNav current-page="home" />
        <FloatingAdminPanel :contacts="contacts" />

        <HomeHero :slides="carouselSlides" />

        <HomeMarquee
            :items="mainMarqueeItems"
            bg-color="bg-sumi"
            text-color="text-washi"
            separator-color="text-matcha"
        />

        <HomePOTracker :preorder-items="preorderItems" />

        <HomeKatalog :latest-collections="latestCollections" />

        <HomeTikTokFeed
            :tiktok-feed="tiktokFeed"
            :tiktok-followers="tiktokFollowers"
        />

        <HomeMarquee
            :items="brandMarqueeItems"
            bg-color="bg-matcha"
            text-color="text-washi"
            reversed
        />

        <HomeHowItWorks />

        <HomeGallery :gallery-slots="gallerySlots" />

        <Footer />
    </div>
</template>

<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import Footer from '@/components/sections/Footer.vue';
import HomeGallery from '@/components/sections/HomeGallery.vue';
import HomeHero from '@/components/sections/HomeHero.vue';
import HomeHowItWorks from '@/components/sections/HomeHowItWorks.vue';
import HomeKatalog from '@/components/sections/HomeKatalog.vue';
import HomeMarquee from '@/components/sections/HomeMarquee.vue';
import HomePOTracker from '@/components/sections/HomePOTracker.vue';
import HomeTikTokFeed from '@/components/sections/HomeTikTokFeed.vue';
import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue';
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue';
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui';
import type { GallerySlot } from '@/types/gallery';
import type {
    HeroCarouselSlide,
    HomeLatestCollectionItem,
    HomePreorderItem,
} from '@/types/home';
import type { TikTokFeedItem, TikTokFollowerSnapshot } from '@/types/tiktok';

type HomePageProps = {
    carouselSlides: HeroCarouselSlide[];
    tiktokFeed: TikTokFeedItem[];
    tiktokFollowers: TikTokFollowerSnapshot | null;
    gallerySlots: GallerySlot[];
    preorderItems: HomePreorderItem[];
    latestCollections: HomeLatestCollectionItem[];
};

defineProps<HomePageProps>();
const page = usePage();

// Contact Data
const contacts = computed<FloatingContact[]>(() => {
    const sharedContacts = page.props.floatingContacts;
    if (!Array.isArray(sharedContacts)) return [];
    return sharedContacts as FloatingContact[];
});

// Orders (Mock data, ideally would come from props/API)
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
    {
        id: '#BGS-2755',
        product: 'Jordan 1 Retro High Bred',
        size: '43',
        status: 'Selesai',
        statusClass: 'px-2 py-1 rounded text-[10px] bg-matcha/20 text-matcha',
        progress: 100,
        progressClass: 'bg-matcha',
    },
    {
        id: '#BGS-2870',
        product: 'NB 574 Navy',
        size: '41',
        status: 'Menunggu',
        statusClass: 'px-2 py-1 rounded text-[10px] bg-sumi/10 text-hai',
        progress: 10,
        progressClass: 'bg-hai/50',
    },
]);

const mainMarqueeItems = [
    'BOGORSNEAKERS',
    "BOGOR'S SNEAKERS",
    'LEGIT VERIFIED',
    'EST. 2019',
    'RARE DROPS',
    'STREET CULTURE',
];

const brandMarqueeItems = [
    'NIKE',
    'ADIDAS',
    'JORDAN',
    'NEW BALANCE',
    'ASICS',
    'VANS',
    'CONVERSE',
];
</script>

<style>
/* Global-ish styles that were in Home.vue but are needed by multiple components or provide general layout */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f7f5f0;
}

::-webkit-scrollbar-thumb {
    background: #4a4a4a;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #1a1a1a;
}
</style>
