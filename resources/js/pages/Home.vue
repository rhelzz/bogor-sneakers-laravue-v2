<template>
  <div class="bg-washi text-sumi font-body antialiased">
    <FloatingMenuNav current-page="home" />
    <FloatingAdminPanel :contacts="contacts" />
    <FloatingOrderPanel :orders="orders" cta-href="#po-tracker" cta-text="Lihat Semua Order" />

    <!-- HERO SECTION -->
    <section id="hero" class="min-h-screen flex pattern-wave relative overflow-hidden">
      <!-- Left Content -->
      <div class="w-1/2 flex flex-col justify-center px-16 lg:px-24 relative z-10">
        <!-- Japanese vertical accent -->
        <div class="absolute left-6 top-1/2 -translate-y-1/2 hidden lg:block">
          <span class="vertical-text text-xs tracking-widest text-hai/50">ボゴールスニーカー</span>
        </div>

        <div class="animate-slide-up" style="animation-delay: 0.1s">
          <div class="flex items-center gap-3 mb-8">
            <span class="w-2 h-2 rounded-full bg-matcha animate-pulse-soft"></span>
            <span class="text-xs tracking-widest text-hai">EST. 2019 - BOGOR, IDN</span>
          </div>
        </div>

        <div class="animate-slide-up" style="animation-delay: 0.2s">
          <h1 class="text-5xl lg:text-7xl font-heading font-bold leading-none tracking-tight mb-6">
            BOGOR'S<br>
            <span class="text-matcha">FINEST</span>
          </h1>
        </div>

        <div class="animate-slide-up" style="animation-delay: 0.3s">
          <p class="text-hai leading-relaxed max-w-md mb-8">
            Sneaker culture meets street identity.
            <span class="text-sumi font-medium">Rare. Local. Legit.</span>
          </p>
        </div>

        <div class="animate-slide-up" style="animation-delay: 0.4s">
          <div class="grid grid-cols-2 gap-6 mb-10">
            <div>
              <p class="text-xs text-hai tracking-wider mb-1">LOCATION</p>
              <p class="text-sm font-medium">Bogor, Jawa Barat</p>
            </div>
            <div>
              <p class="text-xs text-hai tracking-wider mb-1">CATALOG</p>
              <p class="text-sm font-medium">240+ styles</p>
            </div>
            <div>
              <p class="text-xs text-hai tracking-wider mb-1">STATUS</p>
              <p class="text-sm font-medium">Open 09:00-21:00</p>
            </div>
            <div>
              <p class="text-xs text-hai tracking-wider mb-1">AUTH</p>
              <p class="text-sm font-medium">100% Legit Verified</p>
            </div>
          </div>
        </div>

        <div class="animate-slide-up flex items-center gap-4" style="animation-delay: 0.5s">
          <a href="#katalog" class="px-8 py-3 bg-sumi text-washi rounded-full text-sm tracking-wider hover:bg-usuzumi transition-all">
            Lihat Katalog
          </a>
          <span class="flex items-center gap-2 text-xs text-hai">
            <i class="bi bi-arrow-down animate-bounce"></i>
            Scroll untuk eksplor
          </span>
        </div>
      </div>

      <!-- Right Visual - Carousel -->
      <div class="w-1/2 relative bg-sumi pattern-wave flex items-center justify-center group">
        <!-- Carousel Container -->
        <div class="relative w-full h-full flex items-center justify-center px-8">
          <!-- Carousel Slides -->
          <div class="relative w-full h-full max-w-md max-h-96 lg:max-h-96">
            <div
              v-for="(slide, idx) in heroCarousel"
              :key="slide.id"
              class="carousel-slide absolute inset-0 rounded-3xl overflow-hidden img-reveal transition-all duration-1000 ease-in-out"
              :class="idx === currentCarouselIndex ? 'opacity-100 scale-100' : 'opacity-0 scale-95 pointer-events-none'"
            >
              <!-- Placeholder Image -->
              <div class="absolute inset-0 bg-linear-to-br from-kuro to-usuzemi flex items-center justify-center">
                <div class="text-center">
                  <i class="bi bi-image text-5xl text-washi/30 mb-2"></i>
                  <p class="text-washi/50 text-sm">{{ slide.title }}</p>
                </div>
              </div>

              <!-- Gradient Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-kuro/80 via-transparent to-transparent"></div>

              <!-- Info Card -->
              <div class="absolute bottom-8 left-8 right-8 z-10">
                <div class="bg-washi/10 backdrop-blur-md rounded-2xl p-4 border border-washi/20">
                  <p class="text-washi/60 text-xs tracking-widest mb-1">{{ slide.brand }}</p>
                  <p class="text-washi font-bold text-lg line-clamp-2">{{ slide.product }}</p>
                  <p class="text-washi/70 text-xs mt-1">{{ slide.price }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <button
            @click="prevCarousel"
            class="absolute left-4 z-20 w-10 h-10 rounded-full bg-washi/10 backdrop-blur-md flex items-center justify-center text-washi hover:bg-washi/20 transition-all opacity-0 group-hover:opacity-100"
          >
            <i class="bi bi-chevron-left"></i>
          </button>
          <button
            @click="nextCarousel"
            class="absolute right-4 z-20 w-10 h-10 rounded-full bg-washi/10 backdrop-blur-md flex items-center justify-center text-washi hover:bg-washi/20 transition-all opacity-0 group-hover:opacity-100"
          >
            <i class="bi bi-chevron-right"></i>
          </button>

          <!-- Dot Indicators -->
          <div class="absolute bottom-4 inset-x-0 flex justify-center gap-2 z-20">
            <button
              v-for="(slide, idx) in heroCarousel"
              :key="`dot-${slide.id}`"
              @click="currentCarouselIndex = idx"
              class="transition-all"
              :class="idx === currentCarouselIndex ? 'w-8 h-2 bg-washi rounded-full' : 'w-2 h-2 bg-washi/30 rounded-full hover:bg-washi/50'"
            ></button>
          </div>
        </div>
      </div>
    </section>

    <!-- MARQUEE -->
    <div class="bg-sumi text-washi py-4 overflow-hidden">
      <div class="animate-marquee flex items-center gap-12 whitespace-nowrap">
        <span class="text-sm tracking-widest">BOGORSNEAKERS</span>
        <span class="text-matcha">◆</span>
        <span class="text-sm tracking-widest">BOGOR'S FINEST</span>
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
        <span class="text-sm tracking-widest">BOGOR'S FINEST</span>
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
    <section id="po-tracker" class="py-24 px-6 lg:px-16 bg-shironeri pattern-grid"> 
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-16 accent-left">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <span class="w-2 h-2 rounded-full bg-matcha animate-pulse-soft"></span>
              <span class="text-xs tracking-widest text-hai">LIVE PO TRACKER</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-heading font-bold mb-4">Pre-Order<br>Aktif</h2>
          </div>
          <div class="mt-6 lg:mt-0 text-right">
            <p class="text-xs text-hai tracking-wider mb-1">SLOT TERSEDIA</p>
            <p class="text-3xl font-bold text-matcha">23</p>
          </div>
        </div>

        <!-- PO Table -->
        <div class="bg-washi rounded-3xl shadow-xl overflow-hidden border border-sumi/5">
          <!-- Table Header -->
          <div class="hidden lg:grid grid-cols-6 gap-4 px-8 py-4 bg-sumi/5 text-xs tracking-widest text-hai">
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
            class="grid grid-cols-1 lg:grid-cols-6 gap-4 px-8 py-6 border-b border-sumi/5 items-center hover:bg-sumi/3 transition-all"
          >
            <div>
              <p class="font-bold">{{ po.product }}</p>
              <p class="text-xs text-hai">SKU: {{ po.sku }} · Size {{ po.size }}</p>
            </div>
            <p class="text-sm text-hai">{{ po.batch }}</p>
            <div>
              <div class="h-2 bg-sumi/10 rounded-full overflow-hidden">
                <div :class="`h-full rounded-full`" :style="`width: ${po.progress}%; background-color: ${po.progress > 90 ? '#FFA94D' : '#7C8C5A'}`"></div>
              </div>
              <p class="text-xs text-hai mt-1">{{ po.progress }}%</p>
            </div>
            <p :class="po.progress > 90 ? 'text-xl font-bold text-amber-600' : 'text-xl font-bold'">{{ po.slots }}</p>
            <div class="flex gap-1">
              <span class="po-digit px-2 py-1 bg-sumi text-washi rounded text-sm font-bold">{{ String(po.timeLeft.h).padStart(2, '0') }}</span>
              <span class="text-hai">:</span>
              <span class="po-digit px-2 py-1 bg-sumi text-washi rounded text-sm font-bold">{{ String(po.timeLeft.m).padStart(2, '0') }}</span>
              <span class="text-hai">:</span>
              <span class="po-digit px-2 py-1 bg-sumi text-washi rounded text-sm font-bold">{{ String(po.timeLeft.s).padStart(2, '0') }}</span>
            </div>
            <span :class="[po.status === 'Open' ? 'px-3 py-1.5 bg-matcha/20 text-matcha' : po.status === 'Almost Full' ? 'px-3 py-1.5 bg-amber-100 text-amber-700' : 'px-3 py-1.5 bg-sumi/10 text-hai', 'rounded-full text-xs font-medium w-fit']">
              {{ po.status }}
            </span>
          </div>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-3 gap-0 mt-8 rounded-2xl overflow-hidden border border-sumi/5">
          <div class="bg-washi p-8 text-center border-r border-sumi/5">
            <p class="text-xs tracking-widest text-hai mb-2">TOTAL PO AKTIF</p>
            <p class="text-4xl font-bold">{{ poList.filter(p => p.status === 'Open').length }}</p>
          </div>
          <div class="bg-washi p-8 text-center border-r border-sumi/5">
            <p class="text-xs tracking-widest text-hai mb-2">SLOT TERSEDIA</p>
            <p class="text-4xl font-bold text-matcha">{{ poList.reduce((sum, p) => sum + p.slots, 0) }}</p>
          </div>
          <div class="bg-washi p-8 text-center">
            <p class="text-xs tracking-widest text-hai mb-2">BATCH SELESAI</p>
            <p class="text-4xl font-bold">12</p>
          </div>
        </div>
      </div>
    </section>

    <!-- KATALOG SECTION -->
    <section id="katalog" class="py-24 px-6 lg:px-16 bg-washi pattern-hemp"> 
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16 pb-10 accent-top">
          <h2 class="text-4xl lg:text-5xl font-heading font-bold mb-4">Koleksi<br>Terbaru</h2>
          <p class="text-hai max-w-md mx-auto">Temukan sneaker impianmu dari koleksi terlengkap di Bogor</p>
        </div>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap justify-center gap-3 mb-12">
          <button
            v-for="filter in ['all', 'nike', 'adidas', 'jordan', 'nb', 'lokal']"
            :key="filter"
            @click="produktFilter = filter"
            :class="['katalog-filter px-6 py-2.5 rounded-full text-sm tracking-wider transition-all', produktFilter === filter ? 'bg-sumi text-washi' : 'bg-sumi/5 text-usuzumi hover:bg-sumi/10']"
          >
            {{ filter === 'all' ? 'Semua' : filter === 'nb' ? 'New Balance' : filter.charAt(0).toUpperCase() + filter.slice(1) }}
          </button>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="katalogGrid">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="katalog-item bg-shironeri rounded-3xl overflow-hidden card-lift border border-sumi/5"
          >
            <div class="aspect-square bg-sumi/5 relative img-reveal">
              <div class="absolute inset-0 flex items-center justify-center">
                <i class="bi bi-image text-4xl text-hai/30"></i>
              </div>
              <span :class="['absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-medium', product.statusClass]">
                {{ product.status }}
              </span>
            </div>
            <div class="p-5">
              <p :class="['font-bold mb-1', product.status === 'Habis' ? 'text-hai' : '']">{{ product.name }}</p>
              <p class="text-xs text-hai mb-3">Sz. {{ product.size }}</p>
              <div class="flex items-center justify-between">
                <p :class="['text-lg font-bold', product.status === 'Habis' ? 'text-hai line-through' : '']">{{ product.price }}</p>
                <button :disabled="product.status === 'Habis'" class="w-10 h-10 rounded-full bg-sumi text-washi flex items-center justify-center hover:bg-usuzumi transition-all disabled:bg-sumi/20 disabled:cursor-not-allowed">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Load More -->
        <div class="text-center mt-12">
          <button class="px-8 py-3 border-2 border-sumi text-sumi rounded-full text-sm tracking-wider hover:bg-sumi hover:text-washi transition-all">
            Muat Lebih Banyak
          </button>
        </div>
      </div>
    </section>

    <!-- TIKTOK SECTION -->
    <section id="tiktok" class="py-24 px-6 lg:px-16 bg-sumi text-washi pattern-dot-wave">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-12">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <i class="bi bi-tiktok text-xl"></i>
              <span class="text-xs tracking-widest text-washi/60">@bogorsneakers</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-heading font-bold mb-4">TikTok<br>Feed</h2>
          </div>
          <div class="mt-6 lg:mt-0 flex items-center gap-4">
            <div class="text-right">
              <p class="text-3xl font-bold">87.3K</p>
              <p class="text-xs text-washi/60 tracking-wider">Followers</p>
            </div>
            <div class="w-px h-12 bg-washi/20"></div>
            <div class="text-right">
              <p class="text-3xl font-bold">2.4M</p>
              <p class="text-xs text-washi/60 tracking-wider">Views</p>
            </div>
          </div>
        </div>

        <!-- Preset Bar -->
        <div class="flex flex-wrap items-center gap-3 mb-8 p-4 bg-washi/5 rounded-2xl">
          <span class="text-xs tracking-widest text-washi/40 mr-2">PRESET</span>
          <button
            v-for="preset in ['street', 'vintage', 'minimal', 'hype', 'sport']"
            :key="preset"
            @click="tiktokPreset = preset"
            :class="['preset-btn px-4 py-2 rounded-full text-xs tracking-wider transition-all', tiktokPreset === preset ? 'bg-washi text-sumi' : 'bg-washi/10 text-washi/60 hover:bg-washi/20']"
          >
            {{ preset.charAt(0).toUpperCase() + preset.slice(1) }}
          </button>
        </div>

        <!-- TikTok Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4" id="tiktokGrid">
          <div
            v-for="video in tiktokVideos"
            :key="video.id"
            class="tiktok-card aspect-9/16 bg-usuzumi rounded-3xl overflow-hidden relative group"
          >
            <div class="absolute inset-0 bg-linear-to-t from-kuro via-transparent to-transparent"></div>
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-16 h-16 rounded-full bg-washi/10 backdrop-blur flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="bi bi-play-fill text-2xl"></i>
              </div>
            </div>
            <!-- Product tag -->
            <div class="absolute bottom-20 left-4 right-4 bg-washi/10 backdrop-blur rounded-xl p-3">
              <p class="text-sm font-medium">{{ video.product }}</p>
              <p class="text-xs text-washi/60">{{ video.price }}</p>
            </div>
            <!-- Info -->
            <div class="absolute bottom-4 left-4 right-4">
              <p class="font-medium text-sm mb-1">@bogorsneakers</p>
              <p class="text-xs text-washi/60 line-clamp-2">{{ video.title }}</p>
            </div>
            <!-- Actions -->
            <div class="absolute top-4 right-4 flex flex-col gap-3">
              <div class="text-center">
                <div class="w-10 h-10 rounded-full bg-washi/10 backdrop-blur flex items-center justify-center mb-1">
                  <i class="bi bi-heart"></i>
                </div>
                <span class="text-[10px]">{{ video.likes }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
          <div class="bg-washi/5 rounded-2xl p-6 text-center">
            <p class="text-xs text-washi/40 tracking-wider mb-2">TOTAL VIEWS</p>
            <p class="text-2xl font-bold">2.4M</p>
          </div>
          <div class="bg-washi/5 rounded-2xl p-6 text-center">
            <p class="text-xs text-washi/40 tracking-wider mb-2">AVG LIKES</p>
            <p class="text-2xl font-bold">11.8K</p>
          </div>
          <div class="bg-washi/5 rounded-2xl p-6 text-center">
            <p class="text-xs text-washi/40 tracking-wider mb-2">ENGAGEMENT</p>
            <p class="text-2xl font-bold">8.2%</p>
          </div>
          <div class="bg-washi/5 rounded-2xl p-6 text-center">
            <p class="text-xs text-washi/40 tracking-wider mb-2">KONVERSI DM</p>
            <p class="text-2xl font-bold">34%</p>
          </div>
        </div>
      </div>
    </section>

    <!-- MARQUEE 2 -->
    <div class="bg-matcha text-washi py-4 overflow-hidden">
      <div class="animate-marquee flex items-center gap-12 whitespace-nowrap" style="animation-direction: reverse;">
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
    <section id="how-it-works" class="py-24 px-6 lg:px-16 bg-shironeri pattern-bamboo">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16 pb-10 accent-top">
          <h2 class="text-4xl lg:text-5xl font-heading font-bold mb-4">Cara<br>Pesan</h2>
        </div>

        <!-- Steps -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="(step, idx) in steps"
            :key="idx"
            class="bg-washi rounded-3xl p-8 card-lift border border-sumi/5 relative"
          >
            <span :class="`absolute -top-4 -left-4 w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold text-washi ${step.color}`">
              {{ idx + 1 }}
            </span>
            <div :class="`w-16 h-16 rounded-2xl flex items-center justify-center mb-6 ${step.bgColor}`">
              <i :class="`${step.icon} text-2xl ${step.textColor}`"></i>
            </div>
            <h3 class="font-heading font-bold text-lg mb-3">{{ step.title }}</h3>
            <p class="text-sm text-hai leading-relaxed">{{ step.description }}</p>
          </div>
        </div>

        <!-- Guarantees -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
          <div
            v-for="guarantee in guarantees"
            :key="guarantee.id"
            class="bg-washi rounded-2xl p-6 flex items-center gap-4 border border-sumi/5"
          >
            <div :class="`w-12 h-12 rounded-full flex items-center justify-center ${guarantee.bgColor}`">
              <i :class="`${guarantee.icon} text-xl ${guarantee.textColor}`"></i>
            </div>
            <div>
              <p class="font-heading font-bold">{{ guarantee.title }}</p>
              <p class="text-xs text-hai">{{ guarantee.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- GALERI -->
    <section id="galeri" class="py-24 px-6 lg:px-16 bg-washi pattern-plum">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-12">
          <div>
            <h2 class="text-4xl lg:text-5xl font-heading font-bold mb-4">Galeri<br>Karya</h2>
            <p class="text-hai">submit @bogorsneakers</p>
          </div>
          <a href="#" class="mt-6 lg:mt-0 px-6 py-3 bg-sumi text-washi rounded-full text-sm tracking-wider hover:bg-usuzumi transition-all">
            Submit Foto
          </a>
        </div>

        <!-- Masonry Grid -->
        <div class="columns-2 lg:columns-4 gap-4">
          <div
            v-for="gallery in galleryItems"
            :key="gallery.id"
            class="break-inside-avoid mb-4"
          >
            <div class="bg-shironeri rounded-3xl overflow-hidden card-lift border border-sumi/5">
              <div :class="`bg-sumi/5 relative img-reveal flex items-center justify-center ${gallery.aspectClass}`">
                <i class="bi bi-image text-4xl text-hai/30"></i>
              </div>
              <div class="p-4">
                <p class="font-medium text-sm">{{ gallery.title }}</p>
                <p class="text-xs text-hai">{{ gallery.author }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Load More -->
        <div class="text-center mt-12">
          <button class="px-8 py-3 border-2 border-sumi text-sumi rounded-full text-sm tracking-wider hover:bg-sumi hover:text-washi transition-all">
            Muat Lebih Banyak
          </button>
        </div>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-sumi text-washi py-16 px-6 lg:px-16 pattern-koinobori">
      <div class="max-w-7xl mx-auto">
        <!-- Big Text -->
        <div class="mb-16">
          <h2 class="text-6xl lg:text-8xl font-heading font-bold leading-none opacity-10">BOGOR</h2>
          <h2 class="text-6xl lg:text-8xl font-heading font-bold leading-none -mt-4 lg:-mt-8">SNEAKERS</h2>
          <p class="text-sm text-washi/40 tracking-widest mt-4">SINCE 2019 - INDONESIA</p>
        </div>

        <!-- Footer Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-12 border-b border-washi/10">
          <!-- Store -->
          <div>
            <p class="text-xs tracking-widest text-washi/40 mb-4">STORE</p>
            <div class="space-y-2 text-sm">
              <p>Jl. Pajajaran No.88</p>
              <p>Bogor, Jawa Barat</p>
              <p>16143 Indonesia</p>
            </div>
          </div>

          <!-- Hours -->
          <div>
            <p class="text-xs tracking-widest text-washi/40 mb-4">JAM BUKA</p>
            <div class="space-y-2 text-sm">
              <p>Senin-Jumat: 09.00-21.00</p>
              <p>Sabtu-Minggu: 10.00-22.00</p>
            </div>
          </div>

          <!-- Navigation -->
          <div>
            <p class="text-xs tracking-widest text-washi/40 mb-4">NAVIGASI</p>
            <div class="space-y-2 text-sm">
              <a href="#katalog" class="block hover:text-matcha transition-colors">Katalog</a>
              <a href="#po-tracker" class="block hover:text-matcha transition-colors">Live PO</a>
              <a href="#tiktok" class="block hover:text-matcha transition-colors">TikTok Feed</a>
              <a href="#galeri" class="block hover:text-matcha transition-colors">Galeri DIY</a>
            </div>
          </div>

          <!-- Social -->
          <div>
            <p class="text-xs tracking-widest text-washi/40 mb-4">SOSIAL</p>
            <div class="space-y-2 text-sm">
              <a href="#" class="flex items-center gap-2 hover:text-matcha transition-colors">
                <i class="bi bi-instagram"></i> @bogorsneakers
              </a>
              <a href="#" class="flex items-center gap-2 hover:text-matcha transition-colors">
                <i class="bi bi-tiktok"></i> @bogorsneakers
              </a>
              <a href="#" class="flex items-center gap-2 hover:text-matcha transition-colors">
                <i class="bi bi-whatsapp"></i> 0812-XXXX-XXXX
              </a>
            </div>
          </div>
        </div>

        <!-- Bottom -->
        <div class="flex flex-col md:flex-row items-center justify-between pt-8">
          <p class="text-xs text-washi/40">2026 Bogorsneakers. All rights reserved.</p>
          <div class="flex items-center gap-4 mt-4 md:mt-0">
            <span class="text-xs text-washi/40">Made with</span>
            <span class="text-matcha">日本美</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue'
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue'
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue'
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui'

// State Management
const produktFilter = ref('all')
const tiktokPreset = ref('street')
const currentCarouselIndex = ref(0)

// Contact Data
const contacts = ref<FloatingContact[]>([
  { id: 1, name: 'Rizky - Admin', role: 'PO · Order · Ketersediaan', phone: '6281234567890', initial: 'R', color: 'bg-matcha/20 text-matcha' },
  { id: 2, name: 'Farhan - CS', role: 'Komplain · Tracking · Retur', phone: '6289876543210', initial: 'F', color: 'bg-indigo/20 text-indigo' },
  { id: 3, name: 'Dinda - DIY', role: 'Kustom · Desain · Konsultasi', phone: '6285511223344', initial: 'D', color: 'bg-sakura/30 text-sakura' },
])

// Orders
const orders = ref<FloatingOrder[]>([
  { id: '#BGS-2841', product: 'Nike Air Max 97 Silver', size: '42', status: 'Produksi', statusClass: 'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700', progress: 55, progressClass: 'bg-sumi' },
  { id: '#BGS-2790', product: 'Adidas Samba OG White', size: '40', status: 'Dikirim', statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700', progress: 85, progressClass: 'bg-sumi' },
  { id: '#BGS-2755', product: 'Jordan 1 Retro High Bred', size: '43', status: 'Selesai', statusClass: 'px-2 py-1 rounded text-[10px] bg-matcha/20 text-matcha', progress: 100, progressClass: 'bg-matcha' },
  { id: '#BGS-2870', product: 'NB 574 Navy', size: '41', status: 'Menunggu', statusClass: 'px-2 py-1 rounded text-[10px] bg-sumi/10 text-hai', progress: 10, progressClass: 'bg-hai/50' },
])

// PO List
const poList = ref([
  { id: 1, product: 'Nike Air Max 97 Silver', sku: 'BGS-NM97-SLV', size: '40-44', batch: '#04', progress: 78, slots: 5, status: 'Open', timeLeft: { h: 8, m: 47, s: 23 } },
  { id: 2, product: 'Adidas Samba OG White', sku: 'BGS-SB-WHT', size: '39-45', batch: '#02', progress: 48, slots: 18, status: 'Open', timeLeft: { h: 2, m: 14, s: 8 } },
  { id: 3, product: 'New Balance 574 Navy', sku: 'BGS-NB574-NVY', size: '40-43', batch: '#01', progress: 95, slots: 2, status: 'Almost Full', timeLeft: { h: 0, m: 0, s: 0 } },
])

// Products
const products = ref([
  { id: 1, name: 'Nike Air Max 97 Silver', size: '39-44', price: 'Rp 1.850K', status: 'PO', statusClass: 'bg-matcha text-washi', brand: 'nike' },
  { id: 2, name: 'Adidas Samba OG White', size: '39-43', price: 'Rp 1.290K', status: 'Ready', statusClass: 'bg-take text-washi', brand: 'adidas' },
  { id: 3, name: 'Jordan 1 Retro High Bred', size: '40-45', price: 'Rp 2.100K', status: 'PO', statusClass: 'bg-matcha text-washi', brand: 'jordan' },
  { id: 4, name: 'New Balance 574 Navy', size: '39-44', price: 'Rp 980K', status: 'Ready', statusClass: 'bg-take text-washi', brand: 'nb' },
  { id: 5, name: 'Nike Dunk Low Panda', size: '40-45', price: 'Rp 1.650K', status: 'Habis', statusClass: 'bg-hai/50 text-washi', brand: 'nike' },
  { id: 6, name: 'Adidas Forum Low', size: '39-43', price: 'Rp 1.100K', status: 'Ready', statusClass: 'bg-take text-washi', brand: 'adidas' },
  { id: 7, name: 'Ventela Classic White', size: '39-44', price: 'Rp 420K', status: 'Ready', statusClass: 'bg-take text-washi', brand: 'lokal' },
  { id: 8, name: 'Jordan 4 Retro Black Cat', size: '41-45', price: 'Rp 2.450K', status: 'PO', statusClass: 'bg-matcha text-washi', brand: 'jordan' },
])

const filteredProducts = computed(() => {
  return products.value.filter(p => produktFilter.value === 'all' || p.brand === produktFilter.value)
})

// Hero Carousel
const heroCarousel = ref([
  { id: 1, product: 'Nike Air Max 97 Silver', brand: 'NIKE', price: 'Rp 1.850.000', title: 'Air Max 97' },
  { id: 2, product: 'Adidas Samba OG White', brand: 'ADIDAS', price: 'Rp 1.290.000', title: 'Samba OG' },
  { id: 3, product: 'Jordan 1 Retro High Bred', brand: 'JORDAN', price: 'Rp 2.100.000', title: 'Jordan 1' },
  { id: 4, product: 'New Balance 574 Navy', brand: 'NEW BALANCE', price: 'Rp 980.000', title: 'NB 574' },
  { id: 5, product: 'Ventela Classic White', brand: 'LOKAL', price: 'Rp 420.000', title: 'Ventela Classic' },
])

// Carousel Navigation
const nextCarousel = () => {
  currentCarouselIndex.value = (currentCarouselIndex.value + 1) % heroCarousel.value.length
}

const prevCarousel = () => {
  currentCarouselIndex.value = (currentCarouselIndex.value - 1 + heroCarousel.value.length) % heroCarousel.value.length
}

// TikTok Videos
const tiktokVideos = ref([
  { id: 1, product: 'Nike Air Max 97 Silver', price: 'Rp 1.850.000', title: 'Unboxing Nike AM97 Silver Bullet edisi terbatas', likes: '12.4K' },
  { id: 2, product: 'Adidas Samba OG', price: 'Rp 1.290.000', title: 'Review Adidas Samba OG White', likes: '8.1K' },
  { id: 3, product: 'Jordan 1 Retro High', price: 'Rp 2.100.000', title: 'Jordan 1 Bred masih #1', likes: '21.3K' },
  { id: 4, product: 'NB 574 Navy', price: 'Rp 980.000', title: 'New Balance 574 Navy - klasik', likes: '5.6K' },
])

// Steps
const steps = ref([
  { title: 'Pilih Produk', description: 'Browse katalog kami. Filter brand, size, dan warna sesuai selera. Cek stok real-time.', icon: 'bi bi-search', color: 'bg-matcha', bgColor: 'bg-matcha/10', textColor: 'text-matcha' },
  { title: 'DM / WA Kami', description: 'Hubungi via Instagram atau WhatsApp. Kami konfirmasi ketersediaan dalam <5 menit.', icon: 'bi bi-chat-dots', color: 'bg-indigo', bgColor: 'bg-indigo/10', textColor: 'text-indigo' },
  { title: 'Transfer DP', description: 'Bayar DP 50% untuk konfirmasi slot PO. Transfer ke rekening yang terverifikasi.', icon: 'bi bi-credit-card', color: 'bg-take', bgColor: 'bg-take/10', textColor: 'text-take' },
  { title: 'Terima Sepatu', description: 'Sepatu tiba, bayar sisa pelunasan. Cek keaslian bersama kurir atau ambil langsung.', icon: 'bi bi-box2-heart', color: 'bg-sakura', bgColor: 'bg-sakura/20', textColor: 'text-sakura' },
])

// Guarantees
const guarantees = ref([
  { id: 1, title: '100% Legit Guarantee', desc: 'Uang kembali jika palsu', icon: 'bi bi-shield-check', bgColor: 'bg-matcha/10', textColor: 'text-matcha' },
  { id: 2, title: 'Pengiriman Aman', desc: 'Packing bubble wrap + box tebal', icon: 'bi bi-truck', bgColor: 'bg-indigo/10', textColor: 'text-indigo' },
  { id: 3, title: 'CS Responsif', desc: 'Balas <5 menit (09.00-21.00)', icon: 'bi bi-headset', bgColor: 'bg-take/10', textColor: 'text-take' },
])

// Gallery
const galleryItems = ref([
  { id: 1, title: 'Air Max 97 x Jogja Streets', author: '@rizky.jkt', aspectClass: 'aspect-[3/4]' },
  { id: 2, title: 'Samba OG - Bogor Vibe', author: '@adit_sneaks', aspectClass: 'aspect-square' },
  { id: 3, title: 'Jordan 1 Bred - Cold Day', author: '@febri.kicks', aspectClass: 'aspect-[4/5]' },
  { id: 4, title: 'NB 574 Navy x Rain', author: '@putri.sneakers', aspectClass: 'aspect-[3/4]' },
  { id: 5, title: 'Vans Old Skool', author: '@hendra.s', aspectClass: 'aspect-square' },
  { id: 6, title: 'Converse Chuck 70', author: '@sinta.kicks', aspectClass: 'aspect-[4/3]' },
  { id: 7, title: 'Asics Gel-Kayano', author: '@dimas.bgr', aspectClass: 'aspect-[3/4]' },
  { id: 8, title: 'Puma RS-X Effekt', author: '@yuli.sneaks', aspectClass: 'aspect-square' },
])

// PO Timer
const updatePoTimers = () => {
  poList.value.forEach(po => {
    if (po.timeLeft.s > 0 || po.timeLeft.m > 0 || po.timeLeft.h > 0) {
      po.timeLeft.s--

      if (po.timeLeft.s < 0) {
        po.timeLeft.s = 59
        po.timeLeft.m--

        if (po.timeLeft.m < 0) {
          po.timeLeft.m = 59
          po.timeLeft.h--
        }
      }
    }
  })
}

let timerInterval: ReturnType<typeof setInterval> | undefined
let carouselInterval: ReturnType<typeof setInterval> | undefined

onMounted(() => {
  timerInterval = setInterval(updatePoTimers, 1000)
  carouselInterval = setInterval(nextCarousel, 5000)
})

onUnmounted(() => {
  if (timerInterval !== undefined) {
    clearInterval(timerInterval)
  }
  if (carouselInterval !== undefined) {
    clearInterval(carouselInterval)
  }
})
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
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='60' viewBox='0 0 120 60'%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.09' d='M0 30 Q 15 15 30 30 T 60 30 T 90 30 T 120 30'/%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.6' opacity='0.06' d='M0 40 Q 15 25 30 40 T 60 40 T 90 40 T 120 40'/%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.5' opacity='0.04' d='M0 50 Q 15 35 30 50 T 60 50 T 90 50 T 120 50'/%3E%3C/svg%3E");
  background-size: 120px 60px;
}

/* Hemp Leaf Pattern (Asanoha) - for Katalog */
.pattern-hemp {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill='none' stroke='%231A1A1A' stroke-width='0.7' opacity='0.08'%3E%3Cpath d='M30 0 L15 15 M30 0 L45 15 M0 30 L15 15 M0 30 L15 45 M60 30 L45 15 M60 30 L45 45 M30 60 L15 45 M30 60 L45 45'/%3E%3Crect x='20' y='20' width='20' height='20'/%3E%3C/g%3E%3C/svg%3E");
  background-size: 60px 60px;
}

/* Grid Pattern (Kumiko) - for PO Tracker */
.pattern-grid {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 50 50'%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='0.6' opacity='0.09' d='M0 0h50v50H0z M25 0v50 M0 25h50' /%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='0.4' opacity='0.05' d='M10 10 L40 40 M40 10 L10 40' /%3E%3C/svg%3E");
  background-size: 50px 50px;
}

/* Dot Wave Pattern - for TikTok */
.pattern-dot-wave {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cdefs%3E%3CradialGradient id='dotGrad'%3E%3Cstop offset='0%25' stop-color='%23f7f5f0' stop-opacity='0.2'/%3E%3Cstop offset='100%25' stop-color='%23f7f5f0' stop-opacity='0'/3E%3C/radialGradient%3E%3C/defs%3E%3Ccircle cx='20' cy='20' r='2' fill='url(%23dotGrad)' opacity='0.2'/%3E%3Ccircle cx='50' cy='30' r='2' fill='url(%23dotGrad)' opacity='0.2'/%3E%3Ccircle cx='80' cy='20' r='2' fill='url(%23dotGrad)' opacity='0.2'/%3E%3Ccircle cx='30' cy='60' r='2' fill='url(%23dotGrad)' opacity='0.2'/%3E%3Ccircle cx='70' cy='70' r='2' fill='url(%23dotGrad)' opacity='0.2'/%3E%3Ccircle cx='20' cy='90' r='2' fill='url(%23dotGrad)' opacity='0.2'/%3E%3Ccircle cx='80' cy='80' r='2' fill='url(%23dotGrad)' opacity='0.2'/%3E%3C/svg%3E");
  background-size: 100px 100px;
}

/* Bamboo Pattern - for How It Works */
.pattern-bamboo {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='1' opacity='0.08' d='M20 0 L20 80 M60 0 L60 80'/%3E%3Crect x='15' y='10' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.06'/%3E%3Crect x='15' y='35' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.06'/%3E%3Crect x='15' y='60' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.06'/%3E%3Crect x='55' y='20' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.06'/%3E%3Crect x='55' y='45' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.06'/%3E%3C/svg%3E");
  background-size: 80px 80px;
}

/* Plum Blossom Pattern - for Gallery */
.pattern-plum {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Ccircle cx='25' cy='25' r='3' fill='%234a4a4a' opacity='0.05'/%3E%3Ccircle cx='75' cy='25' r='3' fill='%234a4a4a' opacity='0.05'/%3E%3Ccircle cx='25' cy='75' r='3' fill='%234a4a4a' opacity='0.05'/%3E%3Ccircle cx='75' cy='75' r='3' fill='%234a4a4a' opacity='0.05'/%3E%3Ccircle cx='50' cy='50' r='2' fill='%234a4a4a' opacity='0.07'/%3E%3Cpath fill='none' stroke='%234a4a4a' stroke-width='0.5' opacity='0.04' d='M25 25 L75 75 M75 25 L25 75'/%3E%3C/svg%3E");
  background-size: 100px 100px;
}

/* Koinobori Pattern (Carp Streamer) - for Footer */
.pattern-koinobori {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='100' viewBox='0 0 120 100'%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='1.2' opacity='0.08' d='M10 30 Q 30 20 50 30 T 90 30'/%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='1' opacity='0.06' d='M10 55 Q 30 45 50 55 T 90 55'/%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='0.8' opacity='0.04' d='M10 80 Q 30 70 50 80 T 90 80'/%3E%3C/svg%3E");
  background-size: 120px 100px;
}



/* Animations */
@keyframes float {
  0%, 100% {
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
  0%, 100% {
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
  box-shadow: 0 12px 28px rgba(26, 26, 26, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
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
  box-shadow: 0 12px 28px rgba(26, 26, 26, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.panel-slide.open {
  transform: translateY(0) scale(1);
  opacity: 1;
  pointer-events: auto;
  box-shadow: 0 16px 36px rgba(26, 26, 26, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

/* Card hover effect with Japanese refinement */
.card-lift {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 8px rgba(26, 26, 26, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.card-lift:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 24px rgba(26, 26, 26, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

/* Smooth image reveal */
.img-reveal {
  position: relative;
  overflow: hidden;
}

.img-reveal::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(26, 26, 26, 0.03) 0%, transparent 50%);
  pointer-events: none;
}



/* Japanese accent borders */
.accent-left {
  border-left: 3px solid #7C8C5A;
  padding-left: 1rem;
}

.accent-top {
  border-top: 2px solid #7C8C5A;
  padding-top: 1rem;
}

/* Enhanced section dividers */
.section-divider {
  height: 1px;
  background: linear-gradient(to right, transparent, #7C8C5A 20%, #7C8C5A 80%, transparent);
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
