<template>
    <div class="font-body bg-washi text-sumi antialiased">
        <FloatingMenuNav current-page="home" />
        <FloatingAdminPanel :contacts="contacts" />
        <FloatingOrderPanel
            :orders="orders"
            cta-href="#po-tracker"
            cta-text="Lihat Semua Order"
        />

        <!-- HERO SECTION -->
        <section
            id="hero"
            class="pattern-wave relative flex min-h-screen overflow-hidden"
        >
            <!-- Left Content -->
            <div
                class="relative z-10 flex w-1/2 flex-col justify-center px-16 lg:px-24"
            >
                <!-- Japanese vertical accent -->
                <div
                    class="absolute top-1/2 left-6 hidden -translate-y-1/2 lg:block"
                >
                    <span
                        class="vertical-text text-xs tracking-widest text-hai/50"
                        >ボゴールスニーカー</span
                    >
                </div>

                <div class="animate-slide-up" style="animation-delay: 0.1s">
                    <div class="mb-8 flex items-center gap-3">
                        <span
                            class="animate-pulse-soft h-2 w-2 rounded-full bg-matcha"
                        ></span>
                        <span class="text-xs tracking-widest text-hai"
                            >EST. 2019 - BOGOR, IDN</span
                        >
                    </div>
                </div>

                <div class="animate-slide-up" style="animation-delay: 0.2s">
                    <h1
                        class="font-heading mb-6 text-5xl leading-none font-bold tracking-tight lg:text-7xl"
                    >
                        BOGOR'S<br />
                        <span class="text-matcha">SNEAKERS</span>
                    </h1>
                </div>

                <div class="animate-slide-up" style="animation-delay: 0.3s">
                    <p class="mb-8 max-w-md leading-relaxed text-hai">
                        Sneaker culture meets street identity.
                        <span class="font-medium text-sumi"
                            >Rare. Local. Legit.</span
                        >
                    </p>
                </div>

                <div class="animate-slide-up" style="animation-delay: 0.4s">
                    <div class="mb-10 grid grid-cols-2 gap-6">
                        <div>
                            <p class="mb-1 text-xs tracking-wider text-hai">
                                LOCATION
                            </p>
                            <p class="text-sm font-medium">Bogor, Jawa Barat</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs tracking-wider text-hai">
                                CATALOG
                            </p>
                            <p class="text-sm font-medium">240+ styles</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs tracking-wider text-hai">
                                STATUS
                            </p>
                            <p class="text-sm font-medium">Open 09:00-21:00</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs tracking-wider text-hai">
                                AUTH
                            </p>
                            <p class="text-sm font-medium">
                                100% Legit Verified
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="animate-slide-up flex items-center gap-4"
                    style="animation-delay: 0.5s"
                >
                    <a
                        href="#katalog"
                        class="rounded-full bg-sumi px-8 py-3 text-sm tracking-wider text-washi transition-all hover:bg-usuzumi"
                    >
                        Lihat Katalog
                    </a>
                    <span class="flex items-center gap-2 text-xs text-hai">
                        <i class="bi bi-arrow-down animate-bounce"></i>
                        Scroll untuk eksplor
                    </span>
                </div>
            </div>

            <!-- Right Visual - Carousel -->
            <div
                class="pattern-wave group relative flex w-1/2 items-center justify-center bg-sumi"
            >
                <!-- Carousel Container -->
                <div
                    class="relative flex h-full w-full items-center justify-center px-8"
                >
                    <!-- Carousel Slides -->
                    <div
                        class="relative h-full max-h-96 w-full max-w-md lg:max-h-96"
                    >
                        <div
                            v-for="(slide, idx) in heroCarousel"
                            :key="slide.id"
                            class="carousel-slide img-reveal absolute inset-0 overflow-hidden rounded-3xl transition-all duration-1000 ease-in-out"
                            :class="
                                idx === currentCarouselIndex
                                    ? 'scale-100 opacity-100'
                                    : 'pointer-events-none scale-95 opacity-0'
                            "
                        >
                            <img
                                v-if="slide.image_url"
                                :src="slide.image_url"
                                :alt="`Carousel ${idx + 1}`"
                                class="absolute inset-0 h-full w-full object-cover"
                                loading="lazy"
                            />

                            <!-- Placeholder Image -->
                            <div
                                v-else
                                class="absolute inset-0 flex items-center justify-center bg-linear-to-br from-kuro to-usuzemi"
                            >
                                <div class="text-center">
                                    <i
                                        class="bi bi-image mb-2 text-5xl text-washi/30"
                                    ></i>
                                    <p class="text-sm text-washi/50">
                                        Carousel Slide
                                    </p>
                                </div>
                            </div>

                            <!-- Gradient Overlay -->
                            <div
                                class="absolute inset-0 bg-linear-to-t from-kuro/80 via-transparent to-transparent"
                            ></div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <button
                        @click="prevCarousel"
                        class="absolute left-4 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-washi/10 text-washi opacity-0 backdrop-blur-md transition-all group-hover:opacity-100 hover:bg-washi/20"
                    >
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button
                        @click="nextCarousel"
                        class="absolute right-4 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-washi/10 text-washi opacity-0 backdrop-blur-md transition-all group-hover:opacity-100 hover:bg-washi/20"
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    <!-- Dot Indicators -->
                    <div
                        class="absolute inset-x-0 bottom-4 z-20 flex justify-center gap-2"
                    >
                        <button
                            v-for="(slide, idx) in heroCarousel"
                            :key="`dot-${slide.id}`"
                            @click="currentCarouselIndex = idx"
                            class="transition-all"
                            :class="
                                idx === currentCarouselIndex
                                    ? 'h-2 w-8 rounded-full bg-washi'
                                    : 'h-2 w-2 rounded-full bg-washi/30 hover:bg-washi/50'
                            "
                        ></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- MARQUEE -->
        <div class="overflow-hidden bg-sumi py-4 text-washi">
            <div
                class="animate-marquee flex items-center gap-12 whitespace-nowrap"
            >
                <span class="text-sm tracking-widest">BOGORSNEAKERS</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">BOGOR'S SNEAKERS</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">LEGIT VERIFIED</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">EST. 2019</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">RARE DROPS</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">STREET CULTURE</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">BOGORSNEAKERS</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">BOGOR'S SNEAKERS</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">LEGIT VERIFIED</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">EST. 2019</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">RARE DROPS</span>
                <span class="text-matcha">◆</span>
                <span class="text-sm tracking-widest">STREET CULTURE</span>
                <span class="text-matcha">◆</span>
            </div>
        </div>

        <!-- PO TRACKER SECTION -->
        <section
            id="po-tracker"
            class="pattern-grid bg-shironeri px-6 py-24 lg:px-16"
        >
            <div class="mx-auto max-w-7xl">
                <!-- Header -->
                <div
                    class="accent-left mb-16 flex flex-col lg:flex-row lg:items-end lg:justify-between"
                >
                    <div>
                        <div class="mb-4 flex items-center gap-3">
                            <span
                                class="animate-pulse-soft h-2 w-2 rounded-full bg-matcha"
                            ></span>
                            <span class="text-xs tracking-widest text-hai"
                                >LIVE PO TRACKER</span
                            >
                        </div>
                        <h2
                            class="font-heading mb-4 text-4xl font-bold lg:text-5xl"
                        >
                            Pre-Order<br />Aktif
                        </h2>
                    </div>
                    <div class="mt-6 text-right lg:mt-0">
                        <p class="mb-1 text-xs tracking-wider text-hai">
                            SLOT TERSEDIA
                        </p>
                        <p class="text-3xl font-bold text-matcha">23</p>
                    </div>
                </div>

                <!-- PO Table -->
                <div
                    class="overflow-hidden rounded-3xl border border-sumi/5 bg-washi shadow-xl"
                >
                    <!-- Table Header -->
                    <div
                        class="hidden grid-cols-6 gap-4 bg-sumi/5 px-8 py-4 text-xs tracking-widest text-hai lg:grid"
                    >
                        <span>PRODUK</span>
                        <span>BATCH</span>
                        <span>PROGRESS</span>
                        <span>SISA SLOT</span>
                        <span>TUTUP PO</span>
                        <span>STATUS</span>
                    </div>

                    <!-- Rows -->
                    <div
                        v-for="po in poList"
                        :key="po.id"
                        class="grid grid-cols-1 items-center gap-4 border-b border-sumi/5 px-8 py-6 transition-all hover:bg-sumi/3 lg:grid-cols-6"
                    >
                        <div>
                            <p class="font-bold">{{ po.product }}</p>
                            <p class="text-xs text-hai">
                                SKU: {{ po.sku }} · Size {{ po.size }}
                            </p>
                        </div>
                        <p class="text-sm text-hai">{{ po.batch }}</p>
                        <div>
                            <div
                                class="h-2 overflow-hidden rounded-full bg-sumi/10"
                            >
                                <div
                                    :class="`h-full rounded-full`"
                                    :style="`width: ${po.progress}%; background-color: ${po.progress > 90 ? '#FFA94D' : '#7C8C5A'}`"
                                ></div>
                            </div>
                            <p class="mt-1 text-xs text-hai">
                                {{ po.progress }}%
                            </p>
                        </div>
                        <p
                            :class="
                                po.progress > 90
                                    ? 'text-xl font-bold text-amber-600'
                                    : 'text-xl font-bold'
                            "
                        >
                            {{ po.slots }}
                        </p>
                        <div class="flex gap-1">
                            <span
                                class="po-digit rounded bg-sumi px-2 py-1 text-sm font-bold text-washi"
                                >{{
                                    String(po.timeLeft.h).padStart(2, '0')
                                }}</span
                            >
                            <span class="text-hai">:</span>
                            <span
                                class="po-digit rounded bg-sumi px-2 py-1 text-sm font-bold text-washi"
                                >{{
                                    String(po.timeLeft.m).padStart(2, '0')
                                }}</span
                            >
                            <span class="text-hai">:</span>
                            <span
                                class="po-digit rounded bg-sumi px-2 py-1 text-sm font-bold text-washi"
                                >{{
                                    String(po.timeLeft.s).padStart(2, '0')
                                }}</span
                            >
                        </div>
                        <span
                            :class="[
                                po.status === 'Open'
                                    ? 'bg-matcha/20 px-3 py-1.5 text-matcha'
                                    : po.status === 'Almost Full'
                                      ? 'bg-amber-100 px-3 py-1.5 text-amber-700'
                                      : 'bg-sumi/10 px-3 py-1.5 text-hai',
                                'w-fit rounded-full text-xs font-medium',
                            ]"
                        >
                            {{ po.status }}
                        </span>
                    </div>
                </div>

                <!-- Summary -->
                <div
                    class="mt-8 grid grid-cols-3 gap-0 overflow-hidden rounded-2xl border border-sumi/5"
                >
                    <div
                        class="border-r border-sumi/5 bg-washi p-8 text-center"
                    >
                        <p class="mb-2 text-xs tracking-widest text-hai">
                            TOTAL PO AKTIF
                        </p>
                        <p class="text-4xl font-bold">
                            {{
                                poList.filter((p) => p.status === 'Open').length
                            }}
                        </p>
                    </div>
                    <div
                        class="border-r border-sumi/5 bg-washi p-8 text-center"
                    >
                        <p class="mb-2 text-xs tracking-widest text-hai">
                            SLOT TERSEDIA
                        </p>
                        <p class="text-4xl font-bold text-matcha">
                            {{ poList.reduce((sum, p) => sum + p.slots, 0) }}
                        </p>
                    </div>
                    <div class="bg-washi p-8 text-center">
                        <p class="mb-2 text-xs tracking-widest text-hai">
                            BATCH SELESAI
                        </p>
                        <p class="text-4xl font-bold">12</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- KATALOG SECTION -->
        <section id="katalog" class="pattern-hemp bg-washi px-6 py-24 lg:px-16">
            <div class="mx-auto max-w-7xl">
                <!-- Header -->
                <div class="accent-top mb-16 pb-10 text-center">
                    <h2
                        class="font-heading mb-4 text-4xl font-bold lg:text-5xl"
                    >
                        Koleksi<br />Terbaru
                    </h2>
                    <p class="mx-auto max-w-md text-hai">
                        Temukan sneaker impianmu dari koleksi terlengkap di
                        Bogor
                    </p>
                </div>

                <!-- Filter Tabs -->
                <div class="mb-12 flex flex-wrap justify-center gap-3">
                    <button
                        v-for="filter in [
                            'all',
                            'nike',
                            'adidas',
                            'jordan',
                            'nb',
                            'lokal',
                        ]"
                        :key="filter"
                        @click="produktFilter = filter"
                        :class="[
                            'katalog-filter rounded-full px-6 py-2.5 text-sm tracking-wider transition-all',
                            produktFilter === filter
                                ? 'bg-sumi text-washi'
                                : 'bg-sumi/5 text-usuzumi hover:bg-sumi/10',
                        ]"
                    >
                        {{
                            filter === 'all'
                                ? 'Semua'
                                : filter === 'nb'
                                  ? 'New Balance'
                                  : filter.charAt(0).toUpperCase() +
                                    filter.slice(1)
                        }}
                    </button>
                </div>

                <!-- Product Grid -->
                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4"
                    id="katalogGrid"
                >
                    <div
                        v-for="product in filteredProducts"
                        :key="product.id"
                        class="katalog-item card-lift overflow-hidden rounded-3xl border border-sumi/5 bg-shironeri"
                    >
                        <div
                            class="img-reveal relative aspect-square bg-sumi/5"
                        >
                            <div
                                class="absolute inset-0 flex items-center justify-center"
                            >
                                <i class="bi bi-image text-4xl text-hai/30"></i>
                            </div>
                            <span
                                :class="[
                                    'absolute top-4 left-4 rounded-full px-3 py-1 text-xs font-medium',
                                    product.statusClass,
                                ]"
                            >
                                {{ product.status }}
                            </span>
                        </div>
                        <div class="p-5">
                            <p
                                :class="[
                                    'mb-1 font-bold',
                                    product.status === 'Habis'
                                        ? 'text-hai'
                                        : '',
                                ]"
                            >
                                {{ product.name }}
                            </p>
                            <p class="mb-3 text-xs text-hai">
                                Sz. {{ product.size }}
                            </p>
                            <div class="flex items-center justify-between">
                                <p
                                    :class="[
                                        'text-lg font-bold',
                                        product.status === 'Habis'
                                            ? 'text-hai line-through'
                                            : '',
                                    ]"
                                >
                                    {{ product.price }}
                                </p>
                                <button
                                    :disabled="product.status === 'Habis'"
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-sumi text-washi transition-all hover:bg-usuzumi disabled:cursor-not-allowed disabled:bg-sumi/20"
                                >
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Load More -->
                <div class="mt-12 text-center">
                    <button
                        class="rounded-full border-2 border-sumi px-8 py-3 text-sm tracking-wider text-sumi transition-all hover:bg-sumi hover:text-washi"
                    >
                        Muat Lebih Banyak
                    </button>
                </div>
            </div>
        </section>

        <!-- TIKTOK SECTION -->
        <section
            id="tiktok"
            class="pattern-dot-wave bg-sumi px-6 py-24 text-washi lg:px-16"
        >
            <div class="mx-auto max-w-7xl">
                <!-- Header -->
                <div
                    class="mb-12 flex flex-col lg:flex-row lg:items-end lg:justify-between"
                >
                    <div>
                        <div class="mb-4 flex items-center gap-3">
                            <i class="bi bi-tiktok text-xl"></i>
                            <span class="text-xs tracking-widest text-washi/60"
                                >@bogorsneaker</span
                            >
                        </div>
                        <h2
                            class="font-heading mb-4 text-4xl font-bold lg:text-5xl"
                        >
                            TikTok<br />Feed
                        </h2>
                    </div>
                    <div class="mt-6 text-right lg:mt-0">
                        <p class="text-3xl font-bold">
                            {{ tiktokFollowers?.formatted_followers ?? '-' }}
                        </p>
                        <p class="text-xs tracking-wider text-washi/60">
                            Followers
                        </p>
                        <p class="mt-1 text-[11px] text-washi/40">
                            {{
                                tiktokFollowers?.is_stale
                                    ? 'Data cache fallback'
                                    : 'Refresh tiap 15 menit'
                            }}
                        </p>
                    </div>
                </div>

                <!-- Category Bar -->
                <div
                    class="mb-8 flex flex-wrap items-center gap-3 rounded-2xl bg-washi/5 p-4"
                >
                    <span class="mr-2 text-xs tracking-widest text-washi/40"
                        >KATEGORI</span
                    >
                    <button
                        v-for="category in tiktokCategories"
                        :key="category"
                        @click="tiktokCategoryFilter = category"
                        :class="[
                            'preset-btn rounded-full px-4 py-2 text-xs tracking-wider transition-all',
                            tiktokCategoryFilter === category
                                ? 'bg-washi text-sumi'
                                : 'bg-washi/10 text-washi/60 hover:bg-washi/20',
                        ]"
                    >
                        {{
                            category === 'all'
                                ? 'Semua'
                                : toCategoryLabel(category)
                        }}
                    </button>
                </div>

                <!-- TikTok Grid -->
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3" id="tiktokGrid">
                    <div
                        v-if="filteredTiktokVideos.length === 0"
                        class="col-span-1 rounded-3xl border border-washi/10 bg-washi/5 p-8 text-center sm:col-span-2 lg:col-span-3"
                    >
                        <i class="bi bi-collection-play mb-3 block text-4xl text-washi/40"></i>
                        <p class="text-sm text-washi/70">
                            Belum ada video TikTok pada kategori ini.
                        </p>
                    </div>
                    <div
                        v-for="video in filteredTiktokVideos"
                        :key="video.id"
                        class="tiktok-card rounded-3xl border border-washi/10 bg-washi/5 p-4"
                    >
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <span
                                class="rounded-full bg-washi/10 px-3 py-1 text-[10px] tracking-wide text-washi/80"
                            >
                                {{ toCategoryLabel(video.category) }}
                            </span>
                            <a
                                :href="video.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-full bg-washi/10 px-3 py-1 text-[10px] tracking-wide text-washi transition-all hover:bg-washi/20"
                            >
                                Buka TikTok
                            </a>
                        </div>

                        <blockquote
                            class="tiktok-embed mx-auto w-full"
                            :cite="video.url"
                            :data-video-id="video.video_id ?? undefined"
                            style="max-width: 500px; min-width: 280px"
                        >
                            <section>
                                <a
                                    target="_blank"
                                    :title="getEmbedAuthor(video)"
                                    :href="getEmbedProfileUrl(video)"
                                >
                                    {{ getEmbedAuthor(video) }}
                                </a>
                                {{ getEmbedCaption(video) }}
                                <a
                                    target="_blank"
                                    title="Buka video"
                                    :href="getEmbedVideoUrl(video)"
                                >
                                    Lihat di TikTok
                                </a>
                            </section>
                        </blockquote>
                    </div>
                </div>
            </div>
        </section>

        <!-- MARQUEE 2 -->
        <div class="overflow-hidden bg-matcha py-4 text-washi">
            <div
                class="animate-marquee flex items-center gap-12 whitespace-nowrap"
                style="animation-direction: reverse"
            >
                <span class="text-sm tracking-widest">NIKE</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">ADIDAS</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">JORDAN</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">NEW BALANCE</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">ASICS</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">VANS</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">CONVERSE</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">NIKE</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">ADIDAS</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">JORDAN</span>
                <span>◆</span>
                <span class="text-sm tracking-widest">NEW BALANCE</span>
                <span>◆</span>
            </div>
        </div>

        <!-- HOW IT WORKS -->
        <section
            id="how-it-works"
            class="pattern-bamboo bg-shironeri px-6 py-24 lg:px-16"
        >
            <div class="mx-auto max-w-7xl">
                <!-- Header -->
                <div class="accent-top mb-16 pb-10 text-center">
                    <h2
                        class="font-heading mb-4 text-4xl font-bold lg:text-5xl"
                    >
                        Cara<br />Pesan
                    </h2>
                </div>

                <!-- Steps -->
                <div
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        v-for="(step, idx) in steps"
                        :key="idx"
                        class="card-lift relative rounded-3xl border border-sumi/5 bg-washi p-8"
                    >
                        <span
                            :class="`absolute -top-4 -left-4 flex h-12 w-12 items-center justify-center rounded-full text-xl font-bold text-washi ${step.color}`"
                        >
                            {{ idx + 1 }}
                        </span>
                        <div
                            :class="`mb-6 flex h-16 w-16 items-center justify-center rounded-2xl ${step.bgColor}`"
                        >
                            <i
                                :class="`${step.icon} text-2xl ${step.textColor}`"
                            ></i>
                        </div>
                        <h3 class="font-heading mb-3 text-lg font-bold">
                            {{ step.title }}
                        </h3>
                        <p class="text-sm leading-relaxed text-hai">
                            {{ step.description }}
                        </p>
                    </div>
                </div>

                <!-- Guarantees -->
                <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div
                        v-for="guarantee in guarantees"
                        :key="guarantee.id"
                        class="flex items-center gap-4 rounded-2xl border border-sumi/5 bg-washi p-6"
                    >
                        <div
                            :class="`flex h-12 w-12 items-center justify-center rounded-full ${guarantee.bgColor}`"
                        >
                            <i
                                :class="`${guarantee.icon} text-xl ${guarantee.textColor}`"
                            ></i>
                        </div>
                        <div>
                            <p class="font-heading font-bold">
                                {{ guarantee.title }}
                            </p>
                            <p class="text-xs text-hai">{{ guarantee.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- GALERI -->
        <section id="galeri" class="pattern-plum bg-washi px-6 py-24 lg:px-16">
            <div class="mx-auto max-w-7xl">
                <!-- Header -->
                <div
                    class="mb-12 flex flex-col lg:flex-row lg:items-end lg:justify-between"
                >
                    <div>
                        <h2
                            class="font-heading mb-4 text-4xl font-bold lg:text-5xl"
                        >
                            Galeri<br />Karya
                        </h2>
                        <p class="text-hai">Koleksi karya terbaru komunitas.</p>
                    </div>
                </div>

                <!-- Masonry Grid -->
                <div class="columns-2 gap-4 lg:columns-4">
                    <div
                        v-for="gallery in galleryItems"
                        :key="gallery.id"
                        class="mb-4 break-inside-avoid"
                    >
                        <div
                            class="card-lift overflow-hidden rounded-3xl border border-sumi/5 bg-shironeri"
                        >
                            <div
                                :class="`img-reveal relative overflow-hidden bg-sumi/5 ${gallery.aspectClass}`"
                            >
                                <img
                                    v-if="gallery.image_url"
                                    :src="gallery.image_url"
                                    :alt="`Galeri karya slot ${gallery.slot}`"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center"
                                >
                                    <i class="bi bi-image text-4xl text-hai/30"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer
            class="pattern-koinobori bg-sumi px-6 py-16 text-washi lg:px-16"
        >
            <div class="mx-auto max-w-7xl">
                <!-- Big Text -->
                <div class="mb-16">
                    <h2
                        class="font-heading text-6xl leading-none font-bold opacity-10 lg:text-8xl"
                    >
                        BOGOR
                    </h2>
                    <h2
                        class="font-heading -mt-4 text-6xl leading-none font-bold lg:-mt-8 lg:text-8xl"
                    >
                        SNEAKERS
                    </h2>
                    <p class="mt-4 text-sm tracking-widest text-washi/40">
                        SINCE 2019 - INDONESIA
                    </p>
                </div>

                <!-- Footer Grid -->
                <div
                    class="grid grid-cols-1 gap-12 border-b border-washi/10 pb-12 md:grid-cols-2 lg:grid-cols-4"
                >
                    <!-- Store -->
                    <div>
                        <p class="mb-4 text-xs tracking-widest text-washi/40">
                            STORE
                        </p>
                        <div class="space-y-2 text-sm">
                            <p>Jl. Pajajaran No.88</p>
                            <p>Bogor, Jawa Barat</p>
                            <p>16143 Indonesia</p>
                        </div>
                    </div>

                    <!-- Hours -->
                    <div>
                        <p class="mb-4 text-xs tracking-widest text-washi/40">
                            JAM BUKA
                        </p>
                        <div class="space-y-2 text-sm">
                            <p>Senin-Jumat: 09.00-21.00</p>
                            <p>Sabtu-Minggu: 10.00-22.00</p>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div>
                        <p class="mb-4 text-xs tracking-widest text-washi/40">
                            NAVIGASI
                        </p>
                        <div class="space-y-2 text-sm">
                            <a
                                href="#katalog"
                                class="block transition-colors hover:text-matcha"
                                >Katalog</a
                            >
                            <a
                                href="#po-tracker"
                                class="block transition-colors hover:text-matcha"
                                >Live PO</a
                            >
                            <a
                                href="#tiktok"
                                class="block transition-colors hover:text-matcha"
                                >TikTok Feed</a
                            >
                            <a
                                href="#galeri"
                                class="block transition-colors hover:text-matcha"
                                >Galeri DIY</a
                            >
                        </div>
                    </div>

                    <!-- Social -->
                    <div>
                        <p class="mb-4 text-xs tracking-widest text-washi/40">
                            SOSIAL
                        </p>
                        <div class="space-y-2 text-sm">
                            <a
                                href="#"
                                class="flex items-center gap-2 transition-colors hover:text-matcha"
                            >
                                <i class="bi bi-instagram"></i> @bogorsneakers
                            </a>
                            <a
                                href="#"
                                class="flex items-center gap-2 transition-colors hover:text-matcha"
                            >
                                <i class="bi bi-tiktok"></i> @bogorsneakers
                            </a>
                            <a
                                href="#"
                                class="flex items-center gap-2 transition-colors hover:text-matcha"
                            >
                                <i class="bi bi-whatsapp"></i> 0812-XXXX-XXXX
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom -->
                <div
                    class="flex flex-col items-center justify-between pt-8 md:flex-row"
                >
                    <p class="text-xs text-washi/40">
                        2026 Bogorsneakers. All rights reserved.
                    </p>
                    <div class="mt-4 flex items-center gap-4 md:mt-0">
                        <span class="text-xs text-washi/40">Made with</span>
                        <span class="text-matcha">日本美</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue';
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue';
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue';
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui';
import type { GallerySlot as GallerySlotApi } from '@/types/gallery';
import type { TikTokFeedItem, TikTokFollowerSnapshot } from '@/types/tiktok';

type HeroCarouselSlide = {
    id: number;
    image_url: string;
    is_active: boolean;
};

type HomePageProps = {
    carouselSlides: HeroCarouselSlide[];
    tiktokFeed: TikTokFeedItem[];
    tiktokFollowers: TikTokFollowerSnapshot | null;
    gallerySlots: GallerySlotApi[];
};

const props = defineProps<HomePageProps>();

// State Management
const produktFilter = ref('all');
const tiktokCategoryFilter = ref('all');
const currentCarouselIndex = ref(0);

// Contact Data
const contacts = ref<FloatingContact[]>([
    {
        id: 1,
        name: 'Rizky - Admin',
        role: 'PO · Order · Ketersediaan',
        phone: '6281234567890',
        initial: 'R',
        color: 'bg-matcha/20 text-matcha',
    },
    {
        id: 2,
        name: 'Farhan - CS',
        role: 'Komplain · Tracking · Retur',
        phone: '6289876543210',
        initial: 'F',
        color: 'bg-indigo/20 text-indigo',
    },
    {
        id: 3,
        name: 'Dinda - DIY',
        role: 'Kustom · Desain · Konsultasi',
        phone: '6285511223344',
        initial: 'D',
        color: 'bg-sakura/30 text-sakura',
    },
]);

// Orders
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

// PO List
const poList = ref([
    {
        id: 1,
        product: 'Nike Air Max 97 Silver',
        sku: 'BGS-NM97-SLV',
        size: '40-44',
        batch: '#04',
        progress: 78,
        slots: 5,
        status: 'Open',
        timeLeft: { h: 8, m: 47, s: 23 },
    },
    {
        id: 2,
        product: 'Adidas Samba OG White',
        sku: 'BGS-SB-WHT',
        size: '39-45',
        batch: '#02',
        progress: 48,
        slots: 18,
        status: 'Open',
        timeLeft: { h: 2, m: 14, s: 8 },
    },
    {
        id: 3,
        product: 'New Balance 574 Navy',
        sku: 'BGS-NB574-NVY',
        size: '40-43',
        batch: '#01',
        progress: 95,
        slots: 2,
        status: 'Almost Full',
        timeLeft: { h: 0, m: 0, s: 0 },
    },
]);

// Products
const products = ref([
    {
        id: 1,
        name: 'Nike Air Max 97 Silver',
        size: '39-44',
        price: 'Rp 1.850K',
        status: 'PO',
        statusClass: 'bg-matcha text-washi',
        brand: 'nike',
    },
    {
        id: 2,
        name: 'Adidas Samba OG White',
        size: '39-43',
        price: 'Rp 1.290K',
        status: 'Ready',
        statusClass: 'bg-take text-washi',
        brand: 'adidas',
    },
    {
        id: 3,
        name: 'Jordan 1 Retro High Bred',
        size: '40-45',
        price: 'Rp 2.100K',
        status: 'PO',
        statusClass: 'bg-matcha text-washi',
        brand: 'jordan',
    },
    {
        id: 4,
        name: 'New Balance 574 Navy',
        size: '39-44',
        price: 'Rp 980K',
        status: 'Ready',
        statusClass: 'bg-take text-washi',
        brand: 'nb',
    },
    {
        id: 5,
        name: 'Nike Dunk Low Panda',
        size: '40-45',
        price: 'Rp 1.650K',
        status: 'Habis',
        statusClass: 'bg-hai/50 text-washi',
        brand: 'nike',
    },
    {
        id: 6,
        name: 'Adidas Forum Low',
        size: '39-43',
        price: 'Rp 1.100K',
        status: 'Ready',
        statusClass: 'bg-take text-washi',
        brand: 'adidas',
    },
    {
        id: 7,
        name: 'Ventela Classic White',
        size: '39-44',
        price: 'Rp 420K',
        status: 'Ready',
        statusClass: 'bg-take text-washi',
        brand: 'lokal',
    },
    {
        id: 8,
        name: 'Jordan 4 Retro Black Cat',
        size: '41-45',
        price: 'Rp 2.450K',
        status: 'PO',
        statusClass: 'bg-matcha text-washi',
        brand: 'jordan',
    },
]);

const filteredProducts = computed(() => {
    return products.value.filter(
        (p) => produktFilter.value === 'all' || p.brand === produktFilter.value,
    );
});

// Hero Carousel - Initialized from Inertia props
const heroCarousel = ref<HeroCarouselSlide[]>([...props.carouselSlides]);
const tiktokVideos = ref<TikTokFeedItem[]>([...props.tiktokFeed]);
const tiktokFollowers = ref<TikTokFollowerSnapshot | null>(props.tiktokFollowers);

type GalleryItem = {
    id: number;
    slot: number;
    image_url: string | null;
    aspectClass: string;
};

const galleryAspectClassMap: Record<number, string> = {
    1: 'aspect-[3/4]',
    2: 'aspect-square',
    3: 'aspect-[4/5]',
    4: 'aspect-[3/4]',
    5: 'aspect-square',
    6: 'aspect-[4/3]',
    7: 'aspect-[3/4]',
    8: 'aspect-square',
};

const buildGalleryPlaceholders = (): GalleryItem[] => {
    return Array.from({ length: 8 }, (_, index) => {
        const slot = index + 1;

        return {
            id: slot,
            slot,
            image_url: null,
            aspectClass: galleryAspectClassMap[slot] ?? 'aspect-square',
        };
    });
};

const normalizeGalleryItems = (slots: GallerySlotApi[]): GalleryItem[] => {
    const placeholders = buildGalleryPlaceholders();

    const normalizedSlots = [...slots]
        .sort((a, b) => a.slot - b.slot)
        .slice(0, 8)
        .map((slot) => {
            return {
                id: slot.id,
                slot: slot.slot,
                image_url: slot.image_url,
                aspectClass:
                    galleryAspectClassMap[slot.slot] ?? 'aspect-square',
            };
        });

    return placeholders.map((placeholder) => {
        return (
            normalizedSlots.find((slot) => slot.slot === placeholder.slot) ??
            placeholder
        );
    });
};

const galleryItems = ref<GalleryItem[]>(normalizeGalleryItems(props.gallerySlots));

const tiktokCategories = computed(() => {
    const categories = Array.from(
        new Set(
            tiktokVideos.value
                .map((video) => video.category)
                .filter((category): category is string => category !== ''),
        ),
    );

    return ['all', ...categories];
});

const filteredTiktokVideos = computed(() => {
    if (tiktokCategoryFilter.value === 'all') {
        return tiktokVideos.value;
    }

    return tiktokVideos.value.filter(
        (video) => video.category === tiktokCategoryFilter.value,
    );
});

const TIKTOK_EMBED_SCRIPT_ID = 'tiktok-embed-script';

const extractTikTokUsername = (videoUrl: string, fallback?: string | null) => {
    const match = videoUrl.match(/tiktok\.com\/@([^/?]+)/i);

    if (match?.[1]) {
        return match[1];
    }

    const cleanFallback = (fallback ?? '').trim().replace(/^@/, '');

    return cleanFallback !== '' ? cleanFallback : 'bogorsneaker';
};

const getEmbedAuthor = (video: TikTokFeedItem) => {
    const username = extractTikTokUsername(video.url, video.author_name);

    return `@${username}`;
};

const getEmbedProfileUrl = (video: TikTokFeedItem) => {
    const username = extractTikTokUsername(video.url, video.author_name);

    return `https://www.tiktok.com/@${encodeURIComponent(username)}?refer=embed`;
};

const getEmbedVideoUrl = (video: TikTokFeedItem) => {
    try {
        const url = new URL(video.url);
        url.searchParams.set('refer', 'embed');

        return url.toString();
    } catch {
        return video.url;
    }
};

const getEmbedCaption = (video: TikTokFeedItem) => {
    return video.title?.trim() || 'Lihat video terbaru dari Bogor Sneakers.';
};

const disableTikTokAutoplay = () => {
    const tiktokEmbeds = document.querySelectorAll('.tiktok-embed');

    tiktokEmbeds.forEach((embed) => {
        const iframe = embed.querySelector('iframe');

        if (iframe) {
            iframe.addEventListener('load', () => {
                try {
                    const sandboxValue = iframe.getAttribute('sandbox') || '';

                    if (!sandboxValue.includes('allow-scripts')) {
                        iframe.setAttribute('sandbox', sandboxValue + ' allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-presentation');
                    }
                } catch (e) {
                    console.warn('Could not disable autoplay:', e);
                }
            }, { once: true });
        }
    });
};

const refreshTikTokEmbedScript = () => {
    const existing = document.getElementById(TIKTOK_EMBED_SCRIPT_ID);

    if (existing) {
        existing.remove();
    }

    const script = document.createElement('script');
    script.id = TIKTOK_EMBED_SCRIPT_ID;
    script.async = true;
    script.src = 'https://www.tiktok.com/embed.js';

    script.onload = () => {
        setTimeout(() => {
            disableTikTokAutoplay();
        }, 100);
    };

    document.body.appendChild(script);
};

const renderTikTokEmbeds = async () => {
    await nextTick();

    if (filteredTiktokVideos.value.length === 0) {
        return;
    }

    refreshTikTokEmbedScript();

    setTimeout(() => {
        disableTikTokAutoplay();
    }, 200);
};

// Carousel Navigation
const nextCarousel = () => {
    if (heroCarousel.value.length === 0) {
        return;
    }

    currentCarouselIndex.value =
        (currentCarouselIndex.value + 1) % heroCarousel.value.length;
};

const prevCarousel = () => {
    if (heroCarousel.value.length === 0) {
        return;
    }

    currentCarouselIndex.value =
        (currentCarouselIndex.value - 1 + heroCarousel.value.length) %
        heroCarousel.value.length;
};

const toCategoryLabel = (category: string) => {
    if (!category) {
        return '-';
    }

    return category
        .split(/[\s_-]+/)
        .filter(Boolean)
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

// Steps
const steps = ref([
    {
        title: 'Pilih Produk',
        description:
            'Browse katalog kami. Filter brand, size, dan warna sesuai selera. Cek stok real-time.',
        icon: 'bi bi-search',
        color: 'bg-matcha',
        bgColor: 'bg-matcha/10',
        textColor: 'text-matcha',
    },
    {
        title: 'DM / WA Kami',
        description:
            'Hubungi via Instagram atau WhatsApp. Kami konfirmasi ketersediaan dalam <5 menit.',
        icon: 'bi bi-chat-dots',
        color: 'bg-indigo',
        bgColor: 'bg-indigo/10',
        textColor: 'text-indigo',
    },
    {
        title: 'Transfer DP',
        description:
            'Bayar DP 50% untuk konfirmasi slot PO. Transfer ke rekening yang terverifikasi.',
        icon: 'bi bi-credit-card',
        color: 'bg-take',
        bgColor: 'bg-take/10',
        textColor: 'text-take',
    },
    {
        title: 'Terima Sepatu',
        description:
            'Sepatu tiba, bayar sisa pelunasan. Cek keaslian bersama kurir atau ambil langsung.',
        icon: 'bi bi-box2-heart',
        color: 'bg-sakura',
        bgColor: 'bg-sakura/20',
        textColor: 'text-sakura',
    },
]);

// Guarantees
const guarantees = ref([
    {
        id: 1,
        title: '100% Legit Guarantee',
        desc: 'Uang kembali jika palsu',
        icon: 'bi bi-shield-check',
        bgColor: 'bg-matcha/10',
        textColor: 'text-matcha',
    },
    {
        id: 2,
        title: 'Pengiriman Aman',
        desc: 'Packing bubble wrap + box tebal',
        icon: 'bi bi-truck',
        bgColor: 'bg-indigo/10',
        textColor: 'text-indigo',
    },
    {
        id: 3,
        title: 'CS Responsif',
        desc: 'Balas <5 menit (09.00-21.00)',
        icon: 'bi bi-headset',
        bgColor: 'bg-take/10',
        textColor: 'text-take',
    },
]);

// PO Timer
const updatePoTimers = () => {
    poList.value.forEach((po) => {
        if (po.timeLeft.s > 0 || po.timeLeft.m > 0 || po.timeLeft.h > 0) {
            po.timeLeft.s--;

            if (po.timeLeft.s < 0) {
                po.timeLeft.s = 59;
                po.timeLeft.m--;

                if (po.timeLeft.m < 0) {
                    po.timeLeft.m = 59;
                    po.timeLeft.h--;
                }
            }
        }
    });
};

let timerInterval: ReturnType<typeof setInterval> | undefined;
let carouselInterval: ReturnType<typeof setInterval> | undefined;

onMounted(async () => {
    if (!tiktokCategories.value.includes(tiktokCategoryFilter.value)) {
        tiktokCategoryFilter.value = 'all';
    }

    await renderTikTokEmbeds();

    timerInterval = setInterval(updatePoTimers, 1000);
    carouselInterval = setInterval(nextCarousel, 5000);
});

watch(
    filteredTiktokVideos,
    () => {
        void renderTikTokEmbeds();
    },
    { deep: true },
);

onUnmounted(() => {
    if (timerInterval !== undefined) {
        clearInterval(timerInterval);
    }

    if (carouselInterval !== undefined) {
        clearInterval(carouselInterval);
    }
});
</script>

<style scoped>
* {
    box-sizing: border-box;
}

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

/* Japanese pattern backgrounds - Refined */
/* Wave Pattern (Seigaiha) - for Hero */
.pattern-wave {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='60' viewBox='0 0 120 60'%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.16' d='M0 30 Q 15 15 30 30 T 60 30 T 90 30 T 120 30'/%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.6' opacity='0.11' d='M0 40 Q 15 25 30 40 T 60 40 T 90 40 T 120 40'/%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.5' opacity='0.08' d='M0 50 Q 15 35 30 50 T 60 50 T 90 50 T 120 50'/%3E%3C/svg%3E");
    background-size: 120px 60px;
}

/* Hemp Leaf Pattern (Asanoha) - for Katalog */
.pattern-hemp {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill='none' stroke='%231A1A1A' stroke-width='0.7' opacity='0.14'%3E%3Cpath d='M30 0 L15 15 M30 0 L45 15 M0 30 L15 15 M0 30 L15 45 M60 30 L45 15 M60 30 L45 45 M30 60 L15 45 M30 60 L45 45'/%3E%3Crect x='20' y='20' width='20' height='20'/%3E%3C/g%3E%3C/svg%3E");
    background-size: 60px 60px;
}

/* Grid Pattern (Kumiko) - for PO Tracker */
.pattern-grid {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 50 50'%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='0.6' opacity='0.16' d='M0 0h50v50H0z M25 0v50 M0 25h50' /%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='0.4' opacity='0.09' d='M10 10 L40 40 M40 10 L10 40' /%3E%3C/svg%3E");
    background-size: 50px 50px;
}

/* Dot Wave Pattern - for TikTok */
.pattern-dot-wave {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cdefs%3E%3CradialGradient id='dotGrad'%3E%3Cstop offset='0%25' stop-color='%23f7f5f0' stop-opacity='0.32'/%3E%3Cstop offset='100%25' stop-color='%23f7f5f0' stop-opacity='0'/3E%3C/radialGradient%3E%3C/defs%3E%3Ccircle cx='20' cy='20' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='50' cy='30' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='80' cy='20' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='30' cy='60' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='70' cy='70' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='20' cy='90' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='80' cy='80' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3C/svg%3E");
    background-size: 100px 100px;
}

/* Bamboo Pattern - for How It Works */
.pattern-bamboo {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='1' opacity='0.14' d='M20 0 L20 80 M60 0 L60 80'/%3E%3Crect x='15' y='10' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3Crect x='15' y='35' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3Crect x='15' y='60' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3Crect x='55' y='20' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3Crect x='55' y='45' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3C/svg%3E");
    background-size: 80px 80px;
}

/* Plum Blossom Pattern - for Gallery */
.pattern-plum {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Ccircle cx='25' cy='25' r='3' fill='%234a4a4a' opacity='0.1'/%3E%3Ccircle cx='75' cy='25' r='3' fill='%234a4a4a' opacity='0.1'/%3E%3Ccircle cx='25' cy='75' r='3' fill='%234a4a4a' opacity='0.1'/%3E%3Ccircle cx='75' cy='75' r='3' fill='%234a4a4a' opacity='0.1'/%3E%3Ccircle cx='50' cy='50' r='2' fill='%234a4a4a' opacity='0.13'/%3E%3Cpath fill='none' stroke='%234a4a4a' stroke-width='0.5' opacity='0.08' d='M25 25 L75 75 M75 25 L25 75'/%3E%3C/svg%3E");
    background-size: 100px 100px;
}

/* Koinobori Pattern (Carp Streamer) - for Footer */
.pattern-koinobori {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='100' viewBox='0 0 120 100'%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='1.2' opacity='0.15' d='M10 30 Q 30 20 50 30 T 90 30'/%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='1' opacity='0.11' d='M10 55 Q 30 45 50 55 T 90 55'/%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='0.8' opacity='0.08' d='M10 80 Q 30 70 50 80 T 90 80'/%3E%3C/svg%3E");
    background-size: 120px 100px;
}

/* Animations */
@keyframes float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes marquee {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
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

@keyframes kenburns {
    0% {
        transform: scale(1) translateX(0);
    }
    50% {
        transform: scale(1.1) translateX(-2%);
    }
    100% {
        transform: scale(1) translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
}

@keyframes slideOutLeft {
    from {
        opacity: 1;
        transform: translateX(0) scale(1);
    }
    to {
        opacity: 0;
        transform: translateX(-20px) scale(0.95);
    }
}

/* Carousel Styles */
.carousel-slide {
    box-shadow:
        0 12px 28px rgba(26, 26, 26, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-slide-up {
    animation: slideInUp 0.8s ease-out forwards;
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
}

.animate-marquee {
    animation: marquee 30s linear infinite;
}

.animate-pulse-soft {
    animation: pulse-soft 2s ease-in-out infinite;
}

.animate-kenburns {
    animation: kenburns 20s ease-in-out infinite;
}

/* Vertical text */
.vertical-text {
    writing-mode: vertical-rl;
    text-orientation: mixed;
}

/* Panel transitions with Japanese aesthetic */
.panel-slide {
    transform: translateY(10px) scale(0.98);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow:
        0 12px 28px rgba(26, 26, 26, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.panel-slide.open {
    transform: translateY(0) scale(1);
    opacity: 1;
    pointer-events: auto;
    box-shadow:
        0 16px 36px rgba(26, 26, 26, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

/* Card hover effect with Japanese refinement */
.card-lift {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow:
        0 2px 8px rgba(26, 26, 26, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.card-lift:hover {
    transform: translateY(-6px);
    box-shadow:
        0 12px 24px rgba(26, 26, 26, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

/* Smooth image reveal */
.img-reveal {
    position: relative;
    overflow: hidden;
}

.carousel-slide.img-reveal {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
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

/* Japanese accent borders */
.accent-left {
    border-left: 3px solid #7c8c5a;
    padding-left: 1rem;
}

.accent-top {
    border-top: 2px solid #7c8c5a;
    padding-top: 1rem;
}

/* Enhanced section dividers */
.section-divider {
    height: 1px;
    background: linear-gradient(
        to right,
        transparent,
        #7c8c5a 20%,
        #7c8c5a 80%,
        transparent
    );
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .vertical-text {
        display: none;
    }

    .pattern-seigaiha,
    .pattern-asanoha {
        background-size: 50%;
    }
}
</style>
