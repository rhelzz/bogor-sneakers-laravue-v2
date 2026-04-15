<template>
  <div class="font-body bg-washi text-sumi antialiased">
    <FloatingMenuNav current-page="studio-custom" />
    <FloatingAdminPanel
      :contacts="contacts"
      title="HUBUNGI DINDA"
      subtitle="DIY & Customization Specialist"
    />
    <FloatingOrderPanel :orders="orders" />

    <section class="pattern-wave relative overflow-hidden px-6 pb-12 pt-30 lg:px-16">
      <div class="pointer-events-none absolute -top-28 -right-20 h-64 w-64 rounded-full bg-matcha/15 blur-3xl"></div>
      <div class="pointer-events-none absolute -left-24 top-32 h-72 w-72 rounded-full bg-sakura/20 blur-3xl"></div>

      <div class="relative mx-auto max-w-7xl">
        <div class="grid items-end gap-10 lg:grid-cols-[1.15fr_0.85fr]">
          <div>
            <div class="mb-5 flex items-center gap-3 animate-slide-up">
              <span class="h-2 w-2 animate-pulse-soft rounded-full bg-matcha"></span>
              <span class="text-xs tracking-[0.28em] text-hai">BOGORSNEAKER GUEST ORDER</span>
            </div>
            <h1 class="font-heading animate-slide-up text-4xl leading-[0.95] font-bold tracking-tight text-sumi lg:text-6xl" style="animation-delay: 0.1s">
              Custom Model
              <span class="block text-matcha">Guest Order</span>
            </h1>
            <p class="mt-5 max-w-2xl animate-slide-up leading-relaxed text-usuzemi" style="animation-delay: 0.2s">
              Halaman ini difokuskan untuk guest order custom model: pilih model, atur material,
              personalisasi artwork, lalu lanjutkan checkout tanpa akun.
            </p>
            <div class="mt-7 flex animate-slide-up flex-wrap items-center gap-3" style="animation-delay: 0.3s">
              <span class="rounded-full border border-sumi/15 bg-washi/90 px-4 py-2 text-[11px] font-semibold tracking-[0.12em] text-usuzemi">
                LIVE SVG RENDER
              </span>
              <span class="rounded-full border border-sumi/15 bg-washi/90 px-4 py-2 text-[11px] font-semibold tracking-[0.12em] text-usuzemi">
                PREMIUM ARTWORK UPLOAD
              </span>
              <span class="rounded-full border border-sumi/15 bg-washi/90 px-4 py-2 text-[11px] font-semibold tracking-[0.12em] text-usuzemi">
                EXPORT PREVIEW + POLA
              </span>
            </div>
          </div>

          <div class="relative overflow-hidden rounded-3xl border border-sumi/10 bg-sumi px-6 py-7 text-washi shadow-xl">
            <div class="pointer-events-none absolute right-3 bottom-3 text-[78px] leading-none text-washi/8">和</div>
            <p class="vertical-text absolute top-5 right-4 text-[11px] tracking-[0.2em] text-washi/30">スタジオ カスタム</p>
            <p class="text-xs tracking-[0.18em] text-washi/60">STATUS</p>
            <p class="mt-2 text-2xl font-bold text-matcha">Custom Model Guest Order</p>
            <p class="mt-4 text-sm leading-relaxed text-washi/75">
              Pilih model, sesuaikan desain, lalu lanjutkan guest checkout untuk proses order pembeli.
            </p>
            <div class="mt-6 flex items-center gap-2 text-xs text-washi/70">
              <span class="h-2 w-2 animate-pulse rounded-full bg-matcha"></span>
              Katalog terakhir:
              <span class="font-semibold text-washi">{{ catalogTimeLabel }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="pattern-grid px-6 pb-28 lg:px-16">
      <div class="mx-auto grid max-w-7xl gap-6 xl:grid-cols-[1.08fr_0.92fr]">
        <div class="space-y-4">
          <div
            ref="viewerRef"
            class="relative mx-auto aspect-square w-full overflow-hidden rounded-3xl border border-sumi/15 bg-linear-to-br from-white via-shironeri to-washi shadow-[0_28px_70px_-30px_rgba(10,10,10,0.55)]"
            role="button"
            tabindex="0"
            aria-label="Area preview desain"
            @click="handleViewerClick"
            @keydown="handleViewerKeydown"
          >
            <canvas
              ref="previewCanvasRef"
              width="1000"
              height="1000"
              class="h-full w-full"
              aria-hidden="true"
            />

            <div class="pointer-events-none absolute inset-0">
              <template v-for="item in customElements" :key="item.id">
                <div
                  v-if="item.type === 'text'"
                  data-draggable="true"
                  role="button"
                  tabindex="0"
                  class="pointer-events-auto absolute top-0 left-0 select-none whitespace-nowrap font-black leading-none"
                  :class="item.id === activeElementId ? 'ring-2 ring-matcha ring-offset-2 ring-offset-washi' : ''"
                  :style="getTextElementStyle(item)"
                  @pointerdown="startDragElement($event, item.id)"
                  @click.stop="activeElementId = item.id"
                  @keydown.enter.prevent="activeElementId = item.id"
                  @keydown.space.prevent="activeElementId = item.id"
                >
                  {{ item.text }}
                </div>

                <div
                  v-else
                  data-draggable="true"
                  role="button"
                  tabindex="0"
                  class="pointer-events-auto absolute top-0 left-0"
                  :class="item.id === activeElementId ? 'ring-2 ring-matcha ring-offset-2 ring-offset-washi' : ''"
                  :style="getImageElementStyle(item)"
                  @pointerdown="startDragElement($event, item.id)"
                  @click.stop="activeElementId = item.id"
                  @keydown.enter.prevent="activeElementId = item.id"
                  @keydown.space.prevent="activeElementId = item.id"
                >
                  <img
                    :src="item.src"
                    alt="Elemen gambar"
                    class="pointer-events-none h-full w-full object-contain"
                    loading="lazy"
                    decoding="async"
                    draggable="false"
                  />
                </div>

                <div
                  v-if="item.id === activeElementId"
                  class="pointer-events-none absolute top-0 left-0 z-40 rounded-md border-2 border-matcha/85"
                  :style="getActiveElementHandleStyle(item)"
                >
                  <div class="transform-handle-connector"></div>
                  <button
                    type="button"
                    data-transform-handle="true"
                    class="transform-handle transform-handle-rotate pointer-events-auto"
                    aria-label="Putar elemen"
                    @pointerdown.stop.prevent="startRotateTransform($event, item.id)"
                  >
                    ROT
                  </button>
                  <button
                    type="button"
                    data-transform-handle="true"
                    class="transform-handle transform-handle-scale pointer-events-auto"
                    aria-label="Ubah skala elemen"
                    @pointerdown.stop.prevent="startScaleTransform($event, item.id)"
                  >
                    SCL
                  </button>
                </div>
              </template>
            </div>

            <div class="pointer-events-none absolute bottom-4 left-4 rounded-lg border-l-4 border-matcha bg-washi/90 px-3 py-2 text-[11px] font-semibold text-usuzemi shadow-lg backdrop-blur-sm">
              <p>
                ID/WA: <span>{{ phone || '-' }}</span> | Nama: <span>{{ name || '-' }}</span> | Size: <span>{{ shoeSize || '-' }}</span>
              </p>
              <p class="mt-0.5">
                O: <span>{{ selectedOutsoleShort }}</span> | M: <span>{{ selectedMidsoleShort }}</span> | I: <span>{{ selectedInsoleShort }}</span>
              </p>
            </div>

            <div v-if="!lazyCanvasReady" class="absolute inset-0 z-20 flex items-center justify-center bg-sumi/80 text-center text-washi backdrop-blur-sm">
              <div>
                <p class="font-heading text-lg font-bold">Preview Ditunda</p>
                <p class="mt-1 text-xs text-washi/70">Scroll ke area ini untuk memuat aset studio secara lazy.</p>
              </div>
            </div>

            <div v-if="isSyncing" class="absolute inset-0 z-30 flex flex-col items-center justify-center gap-4 bg-sumi/85 text-center text-washi backdrop-blur-sm">
              <div class="h-12 w-12 animate-spin rounded-full border-4 border-washi/20 border-t-matcha"></div>
              <div>
                <p class="font-heading text-lg font-bold">Sinkronisasi Model</p>
                <p class="mt-1 text-sm text-washi/75">Sedang memuat aset SVG model terpilih...</p>
              </div>
            </div>
          </div>

          <p class="rounded-2xl border border-matcha/25 bg-matcha/10 px-4 py-3 text-center text-sm font-semibold text-take">
            Tips: drag elemen langsung di preview, klik bagian sepatu untuk pilih aksen, lalu ubah warnanya cepat.
          </p>
        </div>

        <div class="space-y-5">
          <article class="card-lift rounded-3xl border border-sumi/10 bg-washi/95 p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="text-[11px] font-black tracking-[0.14em] text-hai uppercase">Wizard Guest Checkout</p>
                <h2 class="mt-1 font-heading text-xl leading-tight font-bold text-sumi">{{ currentStepMeta.title }}</h2>
                <p class="mt-1 text-xs leading-relaxed text-usuzemi">{{ currentStepMeta.subtitle }}</p>
              </div>
              <div class="flex flex-col items-end gap-2">
                <p class="rounded-full border border-sumi/15 bg-shironeri px-3 py-1 text-[11px] font-bold tracking-[0.08em] text-usuzemi">
                  Langkah {{ currentStep }} / {{ TOTAL_STEPS }}
                </p>
                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    class="rounded-full border border-sumi/20 px-3 py-1.5 text-[11px] font-black tracking-widest text-usuzemi uppercase transition disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="currentStep === 1"
                    @click="goToPreviousStep"
                  >
                    Back
                  </button>

                  <button
                    v-if="currentStep < TOTAL_STEPS"
                    type="button"
                    class="rounded-full bg-sumi px-3 py-1.5 text-[11px] font-black tracking-widest text-washi uppercase transition hover:bg-kuro"
                    @click="goToNextStep"
                  >
                    Next
                  </button>

                  <span v-else class="text-[11px] font-semibold tracking-[0.06em] text-usuzemi uppercase">
                    Final
                  </span>
                </div>
              </div>
            </div>

            <div class="mt-4 h-2 w-full overflow-hidden rounded-full bg-sumi/10">
              <div class="h-full rounded-full bg-matcha transition-all duration-300" :style="{ width: `${wizardProgress}%` }"></div>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-1 sm:grid-cols-8">
              <div
                v-for="step in wizardSteps"
                :key="`wizard-step-${step.id}`"
                class="rounded-lg border px-1 py-1.5 text-center text-[10px] font-bold"
                :class="step.id === currentStep
                  ? 'border-matcha bg-matcha/20 text-take'
                  : (step.id < currentStep
                    ? 'border-sumi/20 bg-shironeri text-sumi'
                    : 'border-sumi/10 bg-white text-hai')"
              >
                {{ step.id }}
              </div>
            </div>
          </article>

          <article v-if="currentStep === 1" class="card-lift rounded-3xl border border-sumi/10 bg-washi/95 p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-3">
              <h2 class="font-heading text-base font-bold text-sumi">1. Pilih Family Model</h2>
              <button
                type="button"
                class="rounded-full border border-sumi/20 px-3 py-1 text-[11px] font-bold tracking-widest text-usuzemi transition hover:border-sumi hover:text-sumi"
                @click="refreshCatalogAndAssets"
              >
                Sinkron Ulang
              </button>
            </div>

            <p v-if="catalogError" class="mb-3 rounded-xl border border-sakura/30 bg-sakura/10 px-3 py-2 text-xs font-semibold text-sakura">
              {{ catalogError }}
            </p>

            <div class="space-y-3">
              <label class="text-[11px] font-bold tracking-[0.12em] text-hai uppercase">
                Tipe Sol / Family
                <select
                  v-model="activeFolderKey"
                  class="mt-1 h-11 w-full rounded-xl border border-sumi/15 bg-shironeri px-3 text-sm font-semibold text-sumi"
                  :disabled="catalogLoading || catalogFolders.length === 0"
                >
                  <option v-for="folder in catalogFolders" :key="folder.key" :value="folder.key">
                    {{ folder.label }} ({{ folder.model_count }} model)
                  </option>
                </select>
              </label>

              <div>
                <p class="mb-2 text-[11px] font-bold tracking-[0.12em] text-hai uppercase">Nomor Model</p>
                <div class="grid grid-cols-4 gap-2 sm:grid-cols-5">
                  <button
                    v-for="model in availableModels"
                    :key="model.id"
                    type="button"
                    class="rounded-xl border px-2 py-2 text-xs font-extrabold transition"
                    :class="model.id === currentModel
                      ? 'border-sumi bg-sumi text-washi'
                      : 'border-sumi/15 bg-shironeri text-usuzemi hover:border-sumi/35'"
                    @click="currentModel = model.id"
                  >
                    {{ model.label }}
                  </button>
                </div>
              </div>

              <div v-if="currentModelMeta" class="rounded-xl border border-sumi/10 bg-shironeri px-3 py-2 text-xs text-usuzemi">
                <p>
                  Layer aksen: <span class="font-bold text-sumi">{{ currentModelMeta.layers.length }}</span>
                </p>
                <p>
                  Layer pola: <span class="font-bold text-sumi">{{ currentModelMeta.pattern_layers.length }}</span>
                </p>
              </div>
            </div>
          </article>

          <article v-else-if="currentStep === 2" class="card-lift rounded-3xl border border-sumi/10 bg-washi/95 p-5 shadow-sm">
            <h2 class="font-heading text-base font-bold text-sumi">2. Pilih Outsole (Sol Luar)</h2>
            <p class="mt-1 text-xs leading-relaxed text-usuzemi">
              Outsole menentukan grip, ketahanan abrasi, dan karakter pijakan saat dipakai harian.
            </p>

            <div class="mt-4 grid gap-2">
              <button
                v-for="option in outsoleOptions"
                :key="`outsole-${option.key}`"
                type="button"
                class="rounded-2xl border p-3 text-left transition"
                :class="selectedOutsole === option.key
                  ? 'border-matcha bg-matcha/15 shadow-sm'
                  : 'border-sumi/15 bg-shironeri hover:border-sumi/35'"
                @click="selectedOutsole = option.key"
              >
                <p class="text-sm font-black text-sumi">{{ option.title }}</p>
                <p class="mt-1 text-xs leading-relaxed text-usuzemi">{{ option.description }}</p>
                <p class="mt-1 text-[11px] font-semibold text-take">{{ option.highlight }}</p>
              </button>
            </div>
          </article>

          <article v-else-if="currentStep === 3" class="card-lift rounded-3xl border border-sumi/10 bg-washi/95 p-5 shadow-sm">
            <h2 class="font-heading text-base font-bold text-sumi">3. Pilih Midsole (Sol Tengah)</h2>
            <p class="mt-1 text-xs leading-relaxed text-usuzemi">
              Midsole menentukan tingkat bantalan, respons pijakan, dan stabilitas kaki.
            </p>

            <div class="mt-4 grid gap-2">
              <button
                v-for="option in midsoleOptions"
                :key="`midsole-${option.key}`"
                type="button"
                class="rounded-2xl border p-3 text-left transition"
                :class="selectedMidsole === option.key
                  ? 'border-matcha bg-matcha/15 shadow-sm'
                  : 'border-sumi/15 bg-shironeri hover:border-sumi/35'"
                @click="selectedMidsole = option.key"
              >
                <p class="text-sm font-black text-sumi">{{ option.title }}</p>
                <p class="mt-1 text-xs leading-relaxed text-usuzemi">{{ option.description }}</p>
                <p class="mt-1 text-[11px] font-semibold text-take">{{ option.highlight }}</p>
              </button>
            </div>
          </article>

          <article v-else-if="currentStep === 4" class="card-lift rounded-3xl border border-sumi/10 bg-washi/95 p-5 shadow-sm">
            <h2 class="font-heading text-base font-bold text-sumi">4. Pilih Insole (Sol Dalam)</h2>
            <p class="mt-1 text-xs leading-relaxed text-usuzemi">
              Insole berpengaruh besar ke kenyamanan, hygiene, dan dukungan telapak kaki.
            </p>

            <div class="mt-4 grid gap-2">
              <button
                v-for="option in insoleOptions"
                :key="`insole-${option.key}`"
                type="button"
                class="rounded-2xl border p-3 text-left transition"
                :class="selectedInsole === option.key
                  ? 'border-matcha bg-matcha/15 shadow-sm'
                  : 'border-sumi/15 bg-shironeri hover:border-sumi/35'"
                @click="selectedInsole = option.key"
              >
                <p class="text-sm font-black text-sumi">{{ option.title }}</p>
                <p class="mt-1 text-xs leading-relaxed text-usuzemi">{{ option.description }}</p>
                <p class="mt-1 text-[11px] font-semibold text-take">{{ option.highlight }}</p>
              </button>
            </div>
          </article>

          <article v-else-if="currentStep === 5" class="card-lift rounded-3xl border border-sumi/10 bg-washi/95 p-5 shadow-sm">
            <h2 class="font-heading text-base font-bold text-sumi">5. Personalisasi Teks & Artwork</h2>
            <p class="mt-1 text-xs leading-relaxed text-usuzemi">
              Tambahkan teks identitas, logo, atau artwork favorit untuk menyesuaikan karakter pesanan.
            </p>

            <div class="mt-4 grid grid-cols-2 gap-2">
              <button
                type="button"
                class="rounded-xl border border-sumi/25 bg-shironeri px-3 py-2 text-xs font-extrabold tracking-[0.08em] text-usuzemi uppercase transition hover:border-sumi/45 hover:text-sumi"
                @click="addTextElement"
              >
                Tambah Teks
              </button>
              <button
                type="button"
                class="rounded-xl border border-sumi/25 bg-shironeri px-3 py-2 text-xs font-extrabold tracking-[0.08em] text-usuzemi uppercase transition hover:border-sumi/45 hover:text-sumi"
                @click="triggerUpload"
              >
                Upload Artwork
              </button>
            </div>

            <div
              class="mt-4 rounded-2xl border border-dashed px-4 py-4 text-center transition"
              :class="isDropActive ? 'border-matcha bg-matcha/10' : 'border-sumi/25 bg-shironeri/70'"
              @dragenter.prevent="isDropActive = true"
              @dragover.prevent="isDropActive = true"
              @dragleave.prevent="isDropActive = false"
              @drop.prevent="handleDropUpload"
            >
              <p class="text-sm font-semibold text-sumi">Drop artwork ke sini</p>
              <p class="mt-1 text-xs text-usuzemi">PNG, JPG, WEBP. Bisa upload banyak sekaligus.</p>
              <button
                type="button"
                class="mt-3 rounded-full bg-sumi px-4 py-1.5 text-[11px] font-bold tracking-widest text-washi uppercase transition hover:bg-kuro"
                @click="triggerUpload"
              >
                Pilih File
              </button>
            </div>

            <div class="mt-4">
              <div class="mb-2 flex items-center justify-between">
                <p class="text-[11px] font-bold tracking-[0.12em] text-hai uppercase">Koleksi Artwork</p>
                <button
                  v-if="uploadedMedia.length > 0"
                  type="button"
                  class="text-[11px] font-bold tracking-[0.08em] text-sakura uppercase"
                  @click="clearUploads"
                >
                  Bersihkan
                </button>
              </div>

              <div v-if="uploadedMedia.length === 0" class="rounded-xl border border-sumi/10 bg-shironeri px-3 py-4 text-center text-xs font-semibold text-hai">
                Belum ada artwork yang dipilih.
              </div>

              <div v-else class="editor-scroll max-h-54 space-y-2 overflow-y-auto pr-1">
                <article
                  v-for="media in uploadedMedia"
                  :key="media.id"
                  class="rounded-xl border px-2 py-2"
                  :class="media.id === selectedUploadId ? 'border-matcha/60 bg-matcha/10' : 'border-sumi/15 bg-shironeri/80'"
                >
                  <div class="flex gap-2">
                    <img
                      :src="media.src"
                      :alt="media.name"
                      class="h-18 w-18 rounded-lg border border-sumi/10 object-cover"
                      loading="lazy"
                      decoding="async"
                    />
                    <div class="min-w-0 flex-1">
                      <p class="truncate text-xs font-bold text-sumi">{{ media.name }}</p>
                      <p class="mt-0.5 text-[11px] text-usuzemi">{{ media.naturalWidth }} x {{ media.naturalHeight }} px</p>

                      <div class="mt-2 flex flex-wrap gap-1">
                        <span
                          v-for="color in media.palette"
                          :key="`${media.id}-${color}`"
                          class="h-4 w-4 rounded-full border border-sumi/20"
                          :style="{ backgroundColor: color }"
                        ></span>
                      </div>

                      <div class="mt-2 flex flex-wrap gap-1">
                        <button
                          type="button"
                          class="rounded-md bg-sumi px-2 py-1 text-[10px] font-bold tracking-[0.06em] text-washi uppercase"
                          @click="addUploadAsElement(media.id)"
                        >
                          Pasang
                        </button>
                        <button
                          type="button"
                          class="rounded-md border border-sumi/25 px-2 py-1 text-[10px] font-bold tracking-[0.06em] text-usuzemi uppercase"
                          @click="setSelectedUpload(media.id)"
                        >
                          Jadikan Palet
                        </button>
                        <button
                          type="button"
                          class="rounded-md border border-sakura/35 px-2 py-1 text-[10px] font-bold tracking-[0.06em] text-sakura uppercase"
                          @click="removeUpload(media.id)"
                        >
                          Hapus
                        </button>
                      </div>
                    </div>
                  </div>
                </article>
              </div>
            </div>

            <div class="mt-4 rounded-xl border border-matcha/30 bg-matcha/10 p-3">
              <div class="flex items-center justify-between gap-2">
                <p class="text-[11px] font-bold tracking-[0.12em] text-take uppercase">Sumber Palet</p>
                <p class="text-xs font-semibold text-usuzemi">{{ selectedPaletteLabel }}</p>
              </div>
              <div class="mt-2 flex flex-wrap gap-1.5">
                <span
                  v-for="color in randomPalette"
                  :key="`active-palette-${color}`"
                  class="h-5 w-5 rounded-full border border-sumi/20"
                  :style="{ backgroundColor: color }"
                ></span>
              </div>

              <div class="mt-3 grid grid-cols-2 gap-2">
                <button
                  type="button"
                  class="rounded-lg bg-matcha px-3 py-2 text-[11px] font-extrabold tracking-[0.08em] text-washi uppercase transition hover:opacity-90"
                  @click="randomizeLayerColorsFromImage"
                >
                  Generate Warna
                </button>
                <button
                  type="button"
                  class="rounded-lg border border-matcha/45 bg-washi px-3 py-2 text-[11px] font-extrabold tracking-[0.08em] text-take uppercase transition hover:bg-matcha/10"
                  @click="syncPaletteFromActiveImage"
                  :disabled="activeElement?.type !== 'image'"
                >
                  Dari Gambar Aktif
                </button>
              </div>
            </div>
          </article>

          <article v-else-if="currentStep === 6" class="card-lift rounded-3xl border border-sumi/10 bg-washi/95 p-5 shadow-sm">
            <h2 class="font-heading text-base font-bold text-sumi">6. Kontrol Cepat Teks & Logo</h2>
            <p class="mt-1 text-xs leading-relaxed text-usuzemi">
              Edit langsung di preview seperti Canva: drag elemen untuk geser, tarik handle pojok untuk scale, dan handle atas untuk rotate.
            </p>

            <div v-if="!activeElement" class="mt-4 rounded-xl border border-dashed border-sumi/20 bg-shironeri px-3 py-4 text-center text-xs font-semibold text-hai">
              Pilih elemen teks atau gambar di preview untuk mulai edit.
            </div>

            <div v-else class="mt-4 space-y-3">
              <div class="flex items-center justify-between gap-2 rounded-xl border border-sumi/15 bg-shironeri px-3 py-2">
                <p class="text-xs font-black tracking-[0.16em] text-usuzemi uppercase">
                  {{ activeElement.type === 'text' ? 'Mode Teks' : 'Mode Gambar' }}
                </p>
                <div class="flex gap-1">
                  <button
                    type="button"
                    class="rounded-md border border-sumi/20 px-2 py-1 text-[10px] font-bold tracking-[0.06em] text-usuzemi uppercase"
                    @click="duplicateActiveElement"
                  >
                    Duplikat
                  </button>
                  <button
                    type="button"
                    class="rounded-md bg-sakura px-2 py-1 text-[10px] font-bold tracking-[0.06em] text-washi uppercase"
                    @click="removeActiveElement"
                  >
                    Hapus
                  </button>
                </div>
              </div>

              <template v-if="activeElement.type === 'text'">
                <label class="text-[10px] font-bold tracking-[0.08em] text-hai uppercase">
                  Isi teks
                  <textarea
                    :value="activeElement.text"
                    rows="2"
                    class="mt-1 w-full rounded-lg border border-sumi/15 bg-white px-2 py-2 text-sm font-semibold text-sumi"
                    @input="onTextInput"
                  ></textarea>
                </label>

                <div class="grid grid-cols-3 gap-2">
                  <label class="text-[10px] font-bold tracking-[0.08em] text-hai uppercase">
                    Warna
                    <input
                      type="color"
                      :value="activeElement.color"
                      class="mt-1 h-8 w-full cursor-pointer rounded-md border border-sumi/20"
                      @change="onTextColorInput"
                    >
                  </label>
                  <label class="text-[10px] font-bold tracking-[0.08em] text-hai uppercase">
                    Stroke
                    <input
                      type="color"
                      :value="activeElement.strokeColor"
                      class="mt-1 h-8 w-full cursor-pointer rounded-md border border-sumi/20"
                      @change="onTextStrokeColorInput"
                    >
                  </label>
                  <label class="text-[10px] font-bold tracking-[0.08em] text-hai uppercase">
                    Tebal
                    <input
                      type="number"
                      min="0"
                      max="50"
                      :value="activeElement.strokeSize"
                      class="mt-1 h-8 w-full rounded-md border border-sumi/20 bg-white px-2 text-xs"
                      @input="onTextStrokeSizeInput"
                    >
                  </label>
                </div>
              </template>

              <template v-else>
                <div class="grid grid-cols-2 gap-2">
                  <button
                    type="button"
                    class="rounded-lg border border-sumi/20 bg-shironeri px-2 py-2 text-[11px] font-bold tracking-[0.06em] text-usuzemi uppercase"
                    @click="removeActiveImageBackground"
                  >
                    Hapus BG Putih
                  </button>
                  <button
                    type="button"
                    class="rounded-lg border border-sumi/20 bg-shironeri px-2 py-2 text-[11px] font-bold tracking-[0.06em] text-usuzemi uppercase"
                    @click="addActiveImageOutline"
                  >
                    Outline Putih
                  </button>
                </div>

                <label class="text-[10px] font-bold tracking-[0.08em] text-hai uppercase">
                  Opacity
                  <input
                    type="range"
                    min="0.1"
                    max="1"
                    step="0.05"
                    :value="activeElement.opacity"
                    class="mt-2 w-full"
                    @input="onImageOpacityInput"
                  >
                </label>
              </template>

              <p class="rounded-xl border border-matcha/35 bg-matcha/10 px-3 py-2 text-xs font-semibold text-take">
                Gunakan handle hijau di preview: handle kanan-bawah untuk memperbesar/mengecilkan, handle bulat atas untuk memutar elemen.
              </p>

              <div class="grid grid-cols-2 gap-2">
                <button type="button" class="rounded-lg border border-sumi/20 bg-shironeri px-2 py-2 text-[11px] font-bold tracking-[0.06em] text-usuzemi uppercase" @click="resetActiveTransform">
                  Reset Transform
                </button>
                <button type="button" class="rounded-lg border border-sumi/20 bg-shironeri px-2 py-2 text-[11px] font-bold tracking-[0.06em] text-usuzemi uppercase" @click="duplicateActiveElement">
                  Duplicate
                </button>
              </div>
            </div>
          </article>

          <article v-else-if="currentStep === 7" class="card-lift rounded-3xl border border-sumi/10 bg-washi/95 p-5 shadow-sm">
            <h2 class="font-heading text-base font-bold text-sumi">7. Warna Aksen & Outline</h2>

            <button
              type="button"
              class="mt-4 w-full rounded-xl bg-sumi px-3 py-2 text-xs font-extrabold tracking-widest text-washi uppercase shadow-md transition hover:bg-kuro"
              @click="randomizeLayerColorsFromImage"
            >
              Random dari Palette Gambar
            </button>

            <div class="editor-scroll mt-4 max-h-70 space-y-3 overflow-y-auto pr-1">
              <p v-if="layerIds.length === 0" class="rounded-xl border border-sakura/30 bg-sakura/10 px-3 py-2 text-xs font-semibold text-sakura">
                Layer aksen tidak ditemukan pada model ini.
              </p>

              <article
                v-for="id in layerIds"
                :key="`layer-${id}`"
                class="rounded-xl border border-sumi/12 bg-shironeri p-3"
              >
                <div class="flex items-center justify-between gap-2">
                  <p class="text-sm font-bold text-sumi">Aksen {{ id }}</p>
                  <input
                    type="color"
                    :value="layerColors[id] ?? '#ffffff'"
                    class="h-8 w-8 cursor-pointer rounded-full border border-sumi/25"
                    :aria-label="`Warna aksen ${id}`"
                    @change="onLayerColorInput(id, $event)"
                  >
                </div>

                <div class="mt-2 border-t border-sumi/10 pt-2">
                  <label class="flex items-center gap-2 text-xs font-semibold text-usuzemi">
                    <input
                      type="checkbox"
                      :checked="layerOutlines[id]?.active ?? false"
                      @change="onLayerOutlineToggleInput(id, $event)"
                    >
                    Aktifkan outline aksen
                  </label>

                  <div v-if="layerOutlines[id]?.active" class="mt-2 grid grid-cols-2 gap-2">
                    <label class="text-[10px] font-bold tracking-[0.08em] text-hai uppercase">
                      Warna
                      <input
                        type="color"
                        :value="layerOutlines[id]?.color ?? '#000000'"
                        class="mt-1 h-8 w-full cursor-pointer rounded-md border border-sumi/20"
                        @change="onLayerOutlineColorInput(id, $event)"
                      >
                    </label>
                    <label class="text-[10px] font-bold tracking-[0.08em] text-hai uppercase">
                      Tebal
                      <input
                        type="number"
                        min="1"
                        max="50"
                        :value="layerOutlines[id]?.size ?? 2"
                        class="mt-1 h-8 w-full rounded-md border border-sumi/20 bg-white px-2 text-xs"
                        @input="onLayerOutlineSizeInput(id, $event)"
                      >
                    </label>
                  </div>
                </div>
              </article>
            </div>
          </article>

          <article v-else class="card-lift rounded-3xl border-2 border-matcha/45 bg-matcha/10 p-5 shadow-sm">
            <h2 class="font-heading text-base font-bold text-sumi">8. Guest Checkout & Export</h2>
            <p class="mt-1 text-xs leading-relaxed text-usuzemi">
              Lengkapi data pembeli, aktifkan add-on layanan, lalu proses file checkout untuk konfirmasi pesanan.
            </p>

            <div class="mt-4 grid gap-3">
              <label class="text-xs font-bold tracking-[0.08em] text-usuzemi uppercase">
                Nama Lengkap
                <input
                  v-model="name"
                  type="text"
                  class="mt-1 h-11 w-full rounded-xl border border-sumi/20 bg-white px-3 text-sm font-semibold text-sumi"
                  placeholder="Wajib diisi"
                >
              </label>

              <label class="text-xs font-bold tracking-[0.08em] text-usuzemi uppercase">
                Nomor WhatsApp
                <input
                  v-model="phone"
                  type="text"
                  inputmode="numeric"
                  class="mt-1 h-11 w-full rounded-xl border border-sumi/20 bg-white px-3 text-sm font-semibold text-sumi"
                  placeholder="Wajib diisi"
                >
              </label>

              <label class="text-xs font-bold tracking-[0.08em] text-usuzemi uppercase">
                Email
                <input
                  v-model="guestEmail"
                  type="email"
                  class="mt-1 h-11 w-full rounded-xl border border-sumi/20 bg-white px-3 text-sm font-semibold text-sumi"
                  placeholder="Wajib diisi"
                >
              </label>

              <label class="text-xs font-bold tracking-[0.08em] text-usuzemi uppercase">
                Ukuran Sepatu
                <select
                  v-model="shoeSize"
                  class="mt-1 h-11 w-full rounded-xl border border-sumi/20 bg-white px-3 text-sm font-semibold text-sumi"
                >
                  <option value="">Pilih ukuran</option>
                  <option v-for="value in shoeSizes" :key="`size-${value}`" :value="String(value)">
                    Size {{ value }}
                  </option>
                </select>
              </label>

              <label class="text-xs font-bold tracking-[0.08em] text-usuzemi uppercase">
                Alamat Pengiriman
                <textarea
                  v-model="guestAddress"
                  rows="3"
                  class="mt-1 w-full rounded-xl border border-sumi/20 bg-white px-3 py-2 text-sm font-semibold text-sumi"
                  placeholder="Wajib diisi"
                ></textarea>
              </label>

              <label class="text-xs font-bold tracking-[0.08em] text-usuzemi uppercase">
                Catatan Tambahan
                <textarea
                  v-model="guestNotes"
                  rows="2"
                  class="mt-1 w-full rounded-xl border border-sumi/20 bg-white px-3 py-2 text-sm font-semibold text-sumi"
                  placeholder="Opsional"
                ></textarea>
              </label>

              <div class="space-y-2 rounded-xl border border-sumi/15 bg-shironeri p-3">
                <label class="flex items-start gap-3 text-sm font-semibold text-sumi">
                  <input
                    v-model="fastTrackEnabled"
                    type="checkbox"
                    class="mt-1 h-4 w-4 rounded border-sumi/40"
                  >
                  <span>
                    Fast Track Production
                    <span class="mt-0.5 block text-xs font-semibold text-usuzemi">Prioritas antrean produksi (+{{ formatCurrency(FAST_TRACK_FEE) }})</span>
                  </span>
                </label>

                <label class="flex items-start gap-3 text-sm font-semibold text-sumi">
                  <input
                    v-model="customBoxEnabled"
                    type="checkbox"
                    class="mt-1 h-4 w-4 rounded border-sumi/40"
                  >
                  <span>
                    Custom Box Premium
                    <span class="mt-0.5 block text-xs font-semibold text-usuzemi">Box eksklusif untuk gift-ready delivery (+{{ formatCurrency(CUSTOM_BOX_FEE) }})</span>
                  </span>
                </label>
              </div>

              <div class="rounded-xl border border-sumi/15 bg-white p-3">
                <p class="text-[11px] font-bold tracking-[0.12em] text-hai uppercase">Ringkasan Biaya (Dummy)</p>
                <div class="mt-2 space-y-1 text-sm text-usuzemi">
                  <p class="flex items-center justify-between gap-2">
                    <span>Base Custom Pair</span>
                    <span class="font-semibold text-sumi">{{ formatCurrency(BASE_PRODUCT_PRICE) }}</span>
                  </p>
                  <p class="flex items-center justify-between gap-2">
                    <span>Fast Track</span>
                    <span class="font-semibold text-sumi">{{ fastTrackEnabled ? formatCurrency(FAST_TRACK_FEE) : formatCurrency(0) }}</span>
                  </p>
                  <p class="flex items-center justify-between gap-2">
                    <span>Custom Box</span>
                    <span class="font-semibold text-sumi">{{ customBoxEnabled ? formatCurrency(CUSTOM_BOX_FEE) : formatCurrency(0) }}</span>
                  </p>
                </div>
                <div class="mt-3 border-t border-sumi/15 pt-3">
                  <p class="flex items-center justify-between gap-2 text-sm font-black text-sumi">
                    <span>Total Estimasi</span>
                    <span>{{ formatCurrency(guestCheckoutTotal) }}</span>
                  </p>
                </div>
              </div>

              <button
                type="button"
                class="mt-1 w-full rounded-xl bg-matcha px-4 py-3 text-xs font-extrabold tracking-[0.12em] text-washi uppercase shadow-md transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-55"
                :disabled="isSaving || isSyncing"
                @click="handleSave"
              >
                {{ isSaving ? 'Memproses Checkout...' : 'Proses Guest Checkout & Download' }}
              </button>

              <a
                v-if="waLink"
                :href="waLink"
                target="_blank"
                rel="noreferrer"
                class="inline-flex w-full items-center justify-center rounded-xl bg-[#25D366] px-4 py-2.5 text-xs font-extrabold tracking-[0.12em] text-white uppercase"
              >
                Kirim Ringkasan ke WhatsApp Pembeli
              </a>
            </div>
          </article>

        </div>
      </div>
    </section>

    <input
      ref="colorPickerRef"
      type="color"
      class="sr-only"
      @change="onLayerPickerChange"
    >

    <input
      ref="uploadInputRef"
      type="file"
      class="hidden"
      accept="image/*"
      multiple
      @change="onUploadInputChange"
    >

    <transition name="toast-fade">
      <div
        v-if="toastMessage"
        class="fixed bottom-24 left-1/2 z-60 -translate-x-1/2 rounded-full bg-sumi px-4 py-2 text-xs font-semibold text-washi shadow-xl"
        role="status"
        aria-live="polite"
      >
        {{ toastMessage }}
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import type { CSSProperties } from 'vue'

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue'
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue'
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue'
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui'

const CANVAS_SIZE = 1000
const PREVIEW_OFFSET_Y = -25
const WATERMARK_X = 1990
const WATERMARK_Y = 3105
const WATERMARK_ANGLE = 17.2
const TOTAL_STEPS = 8
const BASE_PRODUCT_PRICE = 899000
const FAST_TRACK_FEE = 125000
const CUSTOM_BOX_FEE = 65000

const FALLBACK_PALETTE = ['#0f172a', '#ef4444', '#f8fafc', '#eab308', '#10b981', '#3b82f6']
const WHATSAPP_INTRO = 'Halo kak, berikut ringkasan guest checkout BogorSneaker:'

type LayerId = number

interface LayerOutline {
  active: boolean
  color: string
  size: number
}

interface ElementBase {
  id: string
  x: number
  y: number
  size: number
  rotation: number
  scaleX: number
  scaleY: number
}

interface TextElement extends ElementBase {
  type: 'text'
  text: string
  color: string
  strokeColor: string
  strokeSize: number
}

interface ImageElement extends ElementBase {
  type: 'image'
  src: string
  naturalWidth: number
  naturalHeight: number
  opacity: number
  sourceId: string | null
}

type CustomElement = TextElement | ImageElement

interface DragState {
  id: string
  startClientX: number
  startClientY: number
  initX: number
  initY: number
}

interface TransformState {
  id: string
  mode: 'scale' | 'rotate'
  centerClientX: number
  centerClientY: number
  startDistance: number
  startAngle: number
  initialScaleX: number
  initialScaleY: number
  initialRotation: number
}

interface StudioLayerFile {
  id: number
  file: string
}

interface StudioModelMeta {
  id: number
  label: string
  preview_base_file: string | null
  main_file: string | null
  pattern_base_file: string | null
  layers: StudioLayerFile[]
  pattern_layers: StudioLayerFile[]
}

interface StudioFolderMeta {
  key: string
  label: string
  model_count: number
  models: StudioModelMeta[]
}

interface StudioCatalogResponse {
  generated_at: string
  folders: StudioFolderMeta[]
}

interface UploadedMedia {
  id: string
  name: string
  src: string
  naturalWidth: number
  naturalHeight: number
  palette: string[]
}

interface MaterialOption {
  key: string
  title: string
  shortLabel: string
  description: string
  highlight: string
}

interface WizardStep {
  id: number
  title: string
  subtitle: string
}

const wizardSteps: WizardStep[] = [
  {
    id: 1,
    title: 'Pilih Family Model',
    subtitle: 'Pilih siluet sepatu dan nomor model yang ingin dikustom.',
  },
  {
    id: 2,
    title: 'Pilih Outsole',
    subtitle: 'Tentukan karakter grip dan ketahanan sol luar.',
  },
  {
    id: 3,
    title: 'Pilih Midsole',
    subtitle: 'Atur tingkat bantalan dan respons pijakan.',
  },
  {
    id: 4,
    title: 'Pilih Insole',
    subtitle: 'Optimalkan kenyamanan dan dukungan telapak kaki.',
  },
  {
    id: 5,
    title: 'Personalisasi Artwork',
    subtitle: 'Tambah teks atau artwork untuk identitas desain.',
  },
  {
    id: 6,
    title: 'Kontrol Cepat Teks & Logo',
    subtitle: 'Edit elemen langsung di preview dengan drag, rotate handle, dan scale handle.',
  },
  {
    id: 7,
    title: 'Warna Aksen & Outline',
    subtitle: 'Kustom warna layer aksen langsung dari palette pilihan.',
  },
  {
    id: 8,
    title: 'Guest Checkout & Export',
    subtitle: 'Finalisasi data buyer, add-on layanan, lalu proses checkout.',
  },
]

const outsoleOptions: MaterialOption[] = [
  {
    key: 'rubber',
    title: 'Rubber (Karet)',
    shortLabel: 'Rubber',
    description: 'Paling populer karena awet, antiselip, dan tahan air untuk pemakaian harian.',
    highlight: 'Durabilitas tinggi dan cengkeram kuat.',
  },
  {
    key: 'tpu',
    title: 'TPU (Thermoplastic Polyurethane)',
    shortLabel: 'TPU',
    description: 'Material polimer yang lebih ringan dari karet namun tetap kuat dan stabil.',
    highlight: 'Ringan dengan struktur tetap solid.',
  },
  {
    key: 'tpr',
    title: 'TPR (Thermoplastic Rubber)',
    shortLabel: 'TPR',
    description: 'Kombinasi karet mentah dan plastik dengan traksi yang baik untuk area outdoor.',
    highlight: 'Traksi bagus untuk pemakaian aktif.',
  },
  {
    key: 'pvc',
    title: 'PVC',
    shortLabel: 'PVC',
    description: 'Efisien untuk produksi massal, cocok untuk kebutuhan value-oriented.',
    highlight: 'Biaya efisien dengan tampilan rapi.',
  },
]

const midsoleOptions: MaterialOption[] = [
  {
    key: 'eva',
    title: 'EVA (Ethylene Vinyl Acetate)',
    shortLabel: 'EVA',
    description: 'Busa sangat ringan, lembut, dan fleksibel. Populer untuk sepatu lari serta kasual.',
    highlight: 'Bobot ringan dan nyaman dipakai lama.',
  },
  {
    key: 'pu',
    title: 'PU (Polyurethane)',
    shortLabel: 'PU',
    description: 'Lebih padat dan stabil dibanding EVA dengan ketahanan bentuk yang baik.',
    highlight: 'Support kuat dan stabil.',
  },
  {
    key: 'phylon',
    title: 'Phylon',
    shortLabel: 'Phylon',
    description: 'Versi EVA yang lebih padat dengan respons bantalan lebih baik.',
    highlight: 'Bantalan responsif untuk mobilitas tinggi.',
  },
]

const insoleOptions: MaterialOption[] = [
  {
    key: 'lateks',
    title: 'Lateks',
    shortLabel: 'Lateks',
    description: 'Memberikan keempukan maksimal dan dikenal sebagai bahan premium anti pegal.',
    highlight: 'Empuk dan nyaman untuk pemakaian lama.',
  },
  {
    key: 'memory-foam',
    title: 'Memory Foam',
    shortLabel: 'Memory Foam',
    description: 'Mengikuti bentuk telapak kaki untuk distribusi tekanan yang lebih merata.',
    highlight: 'Menyesuaikan kontur kaki pengguna.',
  },
  {
    key: 'silicone-gel',
    title: 'Silikon / Gel',
    shortLabel: 'Gel',
    description: 'Mendukung area arch support dan tumit untuk menurunkan impact titik tekan.',
    highlight: 'Support ekstra pada area sensitif telapak.',
  },
  {
    key: 'recycled-poly-pu',
    title: 'Recycled Polyester & PU Sheet',
    shortLabel: 'Recycled PU',
    description: 'Kombinasi modern yang lebih ramah lingkungan serta memiliki sifat antibakteri.',
    highlight: 'Sustainability dengan fitur hygiene lebih baik.',
  },
]

const contacts = ref<FloatingContact[]>([
  {
    id: 1,
    name: 'Dinda - DIY Master',
    role: 'Kustom, Desain, Konsultasi',
    phone: '6285511223344',
    initial: 'D',
    color: 'bg-sakura/30 text-sakura',
  },
])

const orders = ref<FloatingOrder[]>([
  {
    id: '#BGS-2841',
    product: 'Nike Air Max 97 Silver',
    size: '42',
    status: 'Produksi',
    statusClass: 'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700',
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
])

const shoeSizes = Array.from({ length: 10 }, (_, idx) => 36 + idx)

const previewCanvasRef = ref<HTMLCanvasElement | null>(null)
const viewerRef = ref<HTMLDivElement | null>(null)
const colorPickerRef = ref<HTMLInputElement | null>(null)
const uploadInputRef = ref<HTMLInputElement | null>(null)

const imageCacheRef = ref<Map<string, HTMLImageElement | null>>(new Map())
const baseImageRef = ref<HTMLImageElement | null>(null)
const layerImagesRef = ref<Map<LayerId, HTMLImageElement>>(new Map())
const patternLayerFilesRef = ref<Map<LayerId, string>>(new Map())
const patternBaseFileRef = ref<string | null>(null)

const drawFrameRef = ref<number | null>(null)
const toastTimerRef = ref<number | null>(null)
const dragRef = ref<DragState | null>(null)
const transformRef = ref<TransformState | null>(null)
const pointerMovedRef = ref(false)
const resizeObserverRef = ref<ResizeObserver | null>(null)
const viewerObserverRef = ref<IntersectionObserver | null>(null)
const uploadObjectUrlsRef = ref<Set<string>>(new Set())
const loadRunRef = ref(0)
const elementCounterRef = ref(0)
const uploadCounterRef = ref(0)
const lastLoadedModelRef = ref<string | null>(null)
const textMeasureCanvasRef = ref<HTMLCanvasElement | null>(null)
const textMeasureContextRef = ref<CanvasRenderingContext2D | null>(null)

const catalogFolders = ref<StudioFolderMeta[]>([])
const catalogLoading = ref(false)
const catalogError = ref<string | null>(null)
const catalogUpdatedAt = ref<string | null>(null)
const activeFolderKey = ref('')
const currentModel = ref<number | null>(null)
const lazyCanvasReady = ref(false)

const isSyncing = ref(false)
const assetRevision = ref(0)

const layerIds = ref<LayerId[]>([])
const layerColors = ref<Record<LayerId, string>>({})
const layerOutlines = ref<Record<LayerId, LayerOutline>>({})
const activeLayerPickId = ref<LayerId | null>(null)

const customElements = ref<CustomElement[]>([])
const activeElementId = ref<string | null>(null)
const uploadedMedia = ref<UploadedMedia[]>([])
const selectedUploadId = ref<string | null>(null)
const currentStep = ref(1)

const selectedOutsole = ref('')
const selectedMidsole = ref('')
const selectedInsole = ref('')

const viewerWidth = ref(520)
const isDropActive = ref(false)

const name = ref('')
const phone = ref('')
const shoeSize = ref('')
const guestEmail = ref('')
const guestAddress = ref('')
const guestNotes = ref('')
const fastTrackEnabled = ref(false)
const customBoxEnabled = ref(false)

const isSaving = ref(false)
const waLink = ref<string | null>(null)
const toastMessage = ref<string | null>(null)

const scale = computed(() => viewerWidth.value / CANVAS_SIZE)

const activeFolder = computed(() => {
  return catalogFolders.value.find((folder) => folder.key === activeFolderKey.value) ?? null
})

const availableModels = computed(() => {
  return activeFolder.value?.models ?? []
})

const currentModelMeta = computed(() => {
  if (currentModel.value === null) {
    return null
  }

  return availableModels.value.find((item) => item.id === currentModel.value) ?? null
})

const activeElement = computed(() => {
  return customElements.value.find((item) => item.id === activeElementId.value) ?? null
})

const selectedOutsoleMeta = computed(() => {
  return outsoleOptions.find((item) => item.key === selectedOutsole.value) ?? null
})

const selectedMidsoleMeta = computed(() => {
  return midsoleOptions.find((item) => item.key === selectedMidsole.value) ?? null
})

const selectedInsoleMeta = computed(() => {
  return insoleOptions.find((item) => item.key === selectedInsole.value) ?? null
})

const selectedOutsoleShort = computed(() => {
  return selectedOutsoleMeta.value?.shortLabel ?? '-'
})

const selectedMidsoleShort = computed(() => {
  return selectedMidsoleMeta.value?.shortLabel ?? '-'
})

const selectedInsoleShort = computed(() => {
  return selectedInsoleMeta.value?.shortLabel ?? '-'
})

const wizardProgress = computed(() => {
  return Math.round((currentStep.value / TOTAL_STEPS) * 100)
})

const currentStepMeta = computed(() => {
  return wizardSteps.find((item) => item.id === currentStep.value) ?? wizardSteps[0]
})

const guestCheckoutTotal = computed(() => {
  let total = BASE_PRODUCT_PRICE

  if (fastTrackEnabled.value) {
    total += FAST_TRACK_FEE
  }

  if (customBoxEnabled.value) {
    total += CUSTOM_BOX_FEE
  }

  return total
})

const randomPalette = computed(() => {
  const selected = uploadedMedia.value.find((item) => item.id === selectedUploadId.value)

  if (selected && selected.palette.length > 0) {
    return selected.palette
  }

  if (activeElement.value && activeElement.value.type === 'image' && activeElement.value.sourceId) {
    const activeSourceId = activeElement.value.sourceId
    const fromActive = uploadedMedia.value.find((item) => item.id === activeSourceId)

    if (fromActive && fromActive.palette.length > 0) {
      return fromActive.palette
    }
  }

  return FALLBACK_PALETTE
})

const selectedPaletteLabel = computed(() => {
  const selected = uploadedMedia.value.find((item) => item.id === selectedUploadId.value)

  if (selected) {
    return selected.name
  }

  return 'Palet Kurasi BogorSneaker'
})

const catalogTimeLabel = computed(() => {
  if (!catalogUpdatedAt.value) {
    return '-'
  }

  const date = new Date(catalogUpdatedAt.value)

  if (Number.isNaN(date.getTime())) {
    return '-'
  }

  return `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`
})

function buildAssetUrl(folder: string, model: number, fileName: string): string {
  return `/shoes-svg/${encodeURIComponent(folder)}/${model}/${encodeURIComponent(fileName)}`
}

function sanitizeFileToken(value: string): string {
  const cleaned = value.replace(/[^a-zA-Z0-9 ]/g, '').trim().replace(/\s+/g, '-')

  return cleaned.length > 0 ? cleaned : 'UNKNOWN'
}

function normalizePhoneForWa(value: string): string {
  const digits = value.replace(/\D/g, '')

  if (digits.startsWith('0')) {
    return `62${digits.slice(1)}`
  }

  if (digits.startsWith('8')) {
    return `62${digits}`
  }

  return digits
}

const rupiahFormatter = new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  maximumFractionDigits: 0,
})

function formatCurrency(value: number): string {
  return rupiahFormatter.format(value)
}

function canContinueFromStep(step: number): boolean {
  if (step === 1) {
    if (!activeFolderKey.value || !currentModelMeta.value) {
      showToast('Pilih family dan model terlebih dahulu.')

      return false
    }

    if (isSyncing.value) {
      showToast('Tunggu sinkronisasi model selesai dulu.')

      return false
    }
  }

  if (step === 2 && !selectedOutsole.value) {
    showToast('Pilih material outsole terlebih dahulu.')

    return false
  }

  if (step === 3 && !selectedMidsole.value) {
    showToast('Pilih material midsole terlebih dahulu.')

    return false
  }

  if (step === 4 && !selectedInsole.value) {
    showToast('Pilih material insole terlebih dahulu.')

    return false
  }

  return true
}

function goToNextStep(): void {
  if (currentStep.value >= TOTAL_STEPS) {
    return
  }

  if (!canContinueFromStep(currentStep.value)) {
    return
  }

  currentStep.value += 1
}

function goToPreviousStep(): void {
  currentStep.value = Math.max(1, currentStep.value - 1)
}

function getTextDims(ctx: CanvasRenderingContext2D, text: string, fontSize: number): { width: number; height: number } {
  ctx.font = `800 ${fontSize}px Hanken Grotesk, sans-serif`
  const metrics = ctx.measureText(text || ' ')

  return {
    width: Math.max(metrics.width, fontSize * 0.5),
    height: fontSize,
  }
}

function getTextMeasureContext(): CanvasRenderingContext2D | null {
  if (textMeasureContextRef.value) {
    return textMeasureContextRef.value
  }

  const canvas = document.createElement('canvas')
  canvas.width = 2
  canvas.height = 2

  textMeasureCanvasRef.value = canvas
  textMeasureContextRef.value = canvas.getContext('2d')

  return textMeasureContextRef.value
}

function getElementBaseSize(item: CustomElement): { width: number; height: number } {
  if (item.type === 'image') {
    const ratio = item.naturalHeight / Math.max(1, item.naturalWidth)

    return {
      width: item.size,
      height: item.size * ratio,
    }
  }

  const measureContext = getTextMeasureContext()

  if (!measureContext) {
    return {
      width: Math.max(item.size * 1.8, 20),
      height: Math.max(item.size, 16),
    }
  }

  return getTextDims(measureContext, item.text, item.size)
}

function getElementScaledSize(item: CustomElement): { width: number; height: number } {
  const base = getElementBaseSize(item)
  const scaleX = Math.abs(item.scaleX ?? 1)
  const scaleY = Math.abs(item.scaleY ?? 1)

  return {
    width: Math.max(base.width * scaleX, 12),
    height: Math.max(base.height * scaleY, 12),
  }
}

function drawFilledLayer(
  image: HTMLImageElement,
  color: string,
  targetWidth: number,
  targetHeight: number,
): HTMLCanvasElement {
  const canvas = document.createElement('canvas')
  canvas.width = targetWidth
  canvas.height = targetHeight

  const ctx = canvas.getContext('2d')

  if (!ctx) {
    return canvas
  }

  ctx.fillStyle = color
  ctx.fillRect(0, 0, targetWidth, targetHeight)
  ctx.globalCompositeOperation = 'destination-in'
  ctx.drawImage(image, 0, 0, targetWidth, targetHeight)
  ctx.globalCompositeOperation = 'source-over'

  return canvas
}

function drawOutlineLayer(
  image: HTMLImageElement,
  color: string,
  thickness: number,
  targetWidth: number,
  targetHeight: number,
): HTMLCanvasElement {
  const canvas = document.createElement('canvas')
  canvas.width = targetWidth
  canvas.height = targetHeight

  const ctx = canvas.getContext('2d')

  if (!ctx) {
    return canvas
  }

  const px = Math.max(1, thickness)
  const offsets: Array<[number, number]> = [
    [-1, -1],
    [0, -1],
    [1, -1],
    [-1, 0],
    [1, 0],
    [-1, 1],
    [0, 1],
    [1, 1],
  ]

  for (const [ox, oy] of offsets) {
    ctx.drawImage(image, ox * px, oy * px, targetWidth, targetHeight)
  }

  ctx.globalCompositeOperation = 'source-in'
  ctx.fillStyle = color
  ctx.fillRect(0, 0, targetWidth, targetHeight)
  ctx.globalCompositeOperation = 'source-over'

  return canvas
}

function showToast(message: string): void {
  toastMessage.value = message

  if (toastTimerRef.value !== null) {
    window.clearTimeout(toastTimerRef.value)
  }

  toastTimerRef.value = window.setTimeout(() => {
    toastMessage.value = null
    toastTimerRef.value = null
  }, 2800)
}

async function loadImage(src: string): Promise<HTMLImageElement | null> {
  const cache = imageCacheRef.value

  if (cache.has(src)) {
    return cache.get(src) ?? null
  }

  const result = await new Promise<HTMLImageElement | null>((resolve) => {
    const image = new Image()
    image.onload = () => resolve(image)
    image.onerror = () => resolve(null)
    image.src = src
  })

  cache.set(src, result)

  return result
}

function setInitialModelSelection(folders: StudioFolderMeta[]): void {
  if (folders.length === 0) {
    activeFolderKey.value = ''
    currentModel.value = null

    return
  }

  const matchedFolder = folders.find((folder) => folder.key === activeFolderKey.value) ?? folders[0]
  activeFolderKey.value = matchedFolder.key

  const stillExists = matchedFolder.models.some((item) => item.id === currentModel.value)
  currentModel.value = stillExists ? currentModel.value : (matchedFolder.models[0]?.id ?? null)
}

async function fetchCatalog(refresh = false): Promise<void> {
  catalogLoading.value = true
  catalogError.value = null

  try {
    const endpoint = refresh ? '/api/studio-custom/catalog?refresh=1' : '/api/studio-custom/catalog'
    const response = await fetch(endpoint, {
      headers: {
        Accept: 'application/json',
      },
    })

    if (!response.ok) {
      throw new Error(`Failed to fetch studio catalog: ${response.status}`)
    }

    const data = (await response.json()) as StudioCatalogResponse

    catalogFolders.value = Array.isArray(data.folders) ? data.folders : []
    catalogUpdatedAt.value = data.generated_at
    setInitialModelSelection(catalogFolders.value)

    if (refresh) {
      showToast('Katalog model berhasil disinkronkan.')
    }
  } catch (error) {
    console.error('Failed to fetch model catalog:', error)
    catalogError.value = 'Gagal memuat katalog model dari aset internal.'
    showToast('Gagal memuat katalog model.')
  } finally {
    catalogLoading.value = false
  }
}

function drawPreview(): void {
  const canvas = previewCanvasRef.value

  if (!canvas) {
    return
  }

  const ctx = canvas.getContext('2d', { alpha: false })

  if (!ctx) {
    return
  }

  ctx.fillStyle = '#ffffff'
  ctx.fillRect(0, 0, CANVAS_SIZE, CANVAS_SIZE)

  for (const id of layerIds.value) {
    const image = layerImagesRef.value.get(id)

    if (!image) {
      continue
    }

    const outline = layerOutlines.value[id]

    if (outline?.active) {
      const outlineLayer = drawOutlineLayer(image, outline.color, outline.size, CANVAS_SIZE, CANVAS_SIZE)
      ctx.drawImage(outlineLayer, 0, PREVIEW_OFFSET_Y)
    }

    const fillLayer = drawFilledLayer(image, layerColors.value[id] ?? '#ffffff', CANVAS_SIZE, CANVAS_SIZE)
    ctx.drawImage(fillLayer, 0, PREVIEW_OFFSET_Y)
  }

  if (baseImageRef.value) {
    ctx.globalCompositeOperation = 'multiply'
    ctx.drawImage(baseImageRef.value, 0, PREVIEW_OFFSET_Y, CANVAS_SIZE, CANVAS_SIZE)
    ctx.globalCompositeOperation = 'source-over'
  }
}

function scheduleDraw(): void {
  if (drawFrameRef.value !== null) {
    return
  }

  drawFrameRef.value = window.requestAnimationFrame(() => {
    drawFrameRef.value = null
    drawPreview()
  })
}

async function loadModelAssets(manualRefresh: boolean): Promise<void> {
  const folder = activeFolder.value
  const model = currentModelMeta.value

  if (!folder || !model) {
    return
  }

  isSyncing.value = true
  waLink.value = null

  const modelKey = `${folder.key}/${model.id}`
  const shouldResetDesign = lastLoadedModelRef.value !== modelKey

  if (shouldResetDesign) {
    activeElementId.value = null
    customElements.value = []
  }

  const runId = loadRunRef.value + 1
  loadRunRef.value = runId

  try {
    let baseImage: HTMLImageElement | null = null

    const baseCandidates = [model.preview_base_file, model.main_file].filter((item): item is string => Boolean(item))

    for (const fileName of baseCandidates) {
      const image = await loadImage(buildAssetUrl(folder.key, model.id, fileName))

      if (image) {
        baseImage = image
        break
      }
    }

    const loadedLayers = await Promise.all(
      model.layers.map(async (layer) => {
        const image = await loadImage(buildAssetUrl(folder.key, model.id, layer.file))

        return {
          layer,
          image,
        }
      }),
    )

    if (runId !== loadRunRef.value) {
      return
    }

    const nextLayerMap = new Map<LayerId, HTMLImageElement>()
    const nextIds: LayerId[] = []
    const nextColors: Record<LayerId, string> = {}
    const nextOutlines: Record<LayerId, LayerOutline> = {}

    for (const { layer, image } of loadedLayers) {
      if (!image) {
        continue
      }

      nextLayerMap.set(layer.id, image)
      nextIds.push(layer.id)

      nextColors[layer.id] = shouldResetDesign
        ? '#ffffff'
        : (layerColors.value[layer.id] ?? '#ffffff')

      nextOutlines[layer.id] = shouldResetDesign
        ? {
            active: false,
            color: '#000000',
            size: 2,
          }
        : (layerOutlines.value[layer.id] ?? {
            active: false,
            color: '#000000',
            size: 2,
          })
    }

    baseImageRef.value = baseImage
    layerImagesRef.value = nextLayerMap
    layerIds.value = nextIds
    layerColors.value = nextColors
    layerOutlines.value = nextOutlines
    activeLayerPickId.value = null

    patternBaseFileRef.value = model.pattern_base_file
    patternLayerFilesRef.value = new Map(model.pattern_layers.map((item) => [item.id, item.file]))

    assetRevision.value += 1
    lastLoadedModelRef.value = modelKey

    if (!baseImage) {
      showToast('Base SVG model tidak ditemukan.')
    }

    if (manualRefresh) {
      showToast('Aset model berhasil disinkronkan.')
    }
  } catch (error) {
    console.error('Failed to load model assets:', error)
    showToast('Gagal memuat aset model.')
  } finally {
    if (runId === loadRunRef.value) {
      isSyncing.value = false
    }
  }
}

function updateElement(id: string, updater: (item: CustomElement) => CustomElement): void {
  customElements.value = customElements.value.map((item) => {
    if (item.id !== id) {
      return item
    }

    return updater(item)
  })
}

function createElementId(): string {
  elementCounterRef.value += 1

  return `el_${elementCounterRef.value}`
}

function createUploadId(): string {
  uploadCounterRef.value += 1

  return `up_${uploadCounterRef.value}`
}

function addTextElement(): void {
  const id = createElementId()
  const item: TextElement = {
    id,
    type: 'text',
    text: 'TEKS BARU',
    color: '#111827',
    strokeColor: '#ffffff',
    strokeSize: 0,
    size: 52,
    rotation: 0,
    scaleX: 1,
    scaleY: 1,
    x: 210,
    y: 230,
  }

  customElements.value = [...customElements.value, item]
  activeElementId.value = id
}

function triggerUpload(): void {
  uploadInputRef.value?.click()
}

function removeUpload(id: string): void {
  uploadedMedia.value = uploadedMedia.value.filter((item) => item.id !== id)

  if (selectedUploadId.value === id) {
    selectedUploadId.value = uploadedMedia.value[0]?.id ?? null
  }

  showToast('Artwork dihapus dari koleksi.')
}

function clearUploads(): void {
  uploadedMedia.value = []
  selectedUploadId.value = null
  showToast('Koleksi artwork dibersihkan.')
}

function setSelectedUpload(id: string): void {
  selectedUploadId.value = id
  showToast('Palette source diperbarui.')
}

function rgbToHex(r: number, g: number, b: number): string {
  const toHex = (value: number): string => value.toString(16).padStart(2, '0')

  return `#${toHex(r)}${toHex(g)}${toHex(b)}`
}

function extractPaletteFromImage(image: HTMLImageElement): string[] {
  const canvas = document.createElement('canvas')

  const maxDim = 180
  const ratio = Math.min(1, maxDim / Math.max(image.naturalWidth, image.naturalHeight))

  canvas.width = Math.max(16, Math.floor(image.naturalWidth * ratio))
  canvas.height = Math.max(16, Math.floor(image.naturalHeight * ratio))

  const ctx = canvas.getContext('2d', { willReadFrequently: true })

  if (!ctx) {
    return []
  }

  ctx.drawImage(image, 0, 0, canvas.width, canvas.height)

  const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data
  const bucket = new Map<string, number>()

  for (let i = 0; i < data.length; i += 16) {
    const alpha = data[i + 3]

    if (alpha < 120) {
      continue
    }

    let r = data[i]
    let g = data[i + 1]
    let b = data[i + 2]

    if ((r > 246 && g > 246 && b > 246) || (r < 12 && g < 12 && b < 12)) {
      continue
    }

    r = Math.min(255, Math.max(0, Math.round(r / 24) * 24))
    g = Math.min(255, Math.max(0, Math.round(g / 24) * 24))
    b = Math.min(255, Math.max(0, Math.round(b / 24) * 24))

    const hex = rgbToHex(r, g, b)
    bucket.set(hex, (bucket.get(hex) ?? 0) + 1)
  }

  const sorted = [...bucket.entries()]
    .sort((a, b) => b[1] - a[1])
    .slice(0, 8)
    .map(([color]) => color)

  return sorted.length > 0 ? sorted : FALLBACK_PALETTE
}

async function processUploadFiles(files: File[]): Promise<void> {
  const accepted = files.filter((file) => file.type.startsWith('image/'))

  if (accepted.length === 0) {
    showToast('File harus berupa gambar.')

    return
  }

  const nextUploads: UploadedMedia[] = []

  for (const file of accepted) {
    const src = URL.createObjectURL(file)
    uploadObjectUrlsRef.value.add(src)

    const image = await loadImage(src)

    if (!image) {
      URL.revokeObjectURL(src)
      uploadObjectUrlsRef.value.delete(src)
      continue
    }

    const palette = extractPaletteFromImage(image)

    nextUploads.push({
      id: createUploadId(),
      name: file.name,
      src,
      naturalWidth: image.naturalWidth,
      naturalHeight: image.naturalHeight,
      palette,
    })
  }

  if (nextUploads.length === 0) {
    showToast('Tidak ada gambar yang bisa diproses.')

    return
  }

  uploadedMedia.value = [...nextUploads, ...uploadedMedia.value]
  selectedUploadId.value = nextUploads[0]?.id ?? selectedUploadId.value

  showToast(`${nextUploads.length} artwork berhasil ditambahkan.`)
}

async function onUploadInputChange(event: Event): Promise<void> {
  const target = event.target as HTMLInputElement
  const files = target.files ? Array.from(target.files) : []

  await processUploadFiles(files)
  target.value = ''
}

async function handleDropUpload(event: DragEvent): Promise<void> {
  isDropActive.value = false

  const files = event.dataTransfer?.files ? Array.from(event.dataTransfer.files) : []
  await processUploadFiles(files)
}

function addUploadAsElement(uploadId: string): void {
  const media = uploadedMedia.value.find((item) => item.id === uploadId)

  if (!media) {
    return
  }

  const id = createElementId()
  const item: ImageElement = {
    id,
    type: 'image',
    src: media.src,
    naturalWidth: media.naturalWidth,
    naturalHeight: media.naturalHeight,
    size: 210,
    rotation: 0,
    scaleX: 1,
    scaleY: 1,
    x: 170,
    y: 180,
    opacity: 1,
    sourceId: media.id,
  }

  customElements.value = [...customElements.value, item]
  activeElementId.value = id
  selectedUploadId.value = media.id
  showToast('Gambar ditambahkan ke canvas.')
}

function applyPaletteToLayers(palette: string[]): void {
  if (palette.length === 0) {
    showToast('Palette belum tersedia.')

    return
  }

  layerColors.value = layerIds.value.reduce<Record<LayerId, string>>((carry, id) => {
    const color = palette[Math.floor(Math.random() * palette.length)] ?? '#ffffff'
    carry[id] = color

    return carry
  }, {})

  showToast('Warna aksen di-generate dari palette gambar.')
}

function randomizeLayerColorsFromImage(): void {
  applyPaletteToLayers(randomPalette.value)
}

async function syncPaletteFromActiveImage(): Promise<void> {
  if (!activeElement.value || activeElement.value.type !== 'image') {
    showToast('Pilih elemen gambar terlebih dahulu.')

    return
  }

  const image = await loadImage(activeElement.value.src)

  if (!image) {
    showToast('Gambar aktif tidak bisa diproses.')

    return
  }

  const palette = extractPaletteFromImage(image)
  applyPaletteToLayers(palette)
}

function updateActiveText(patch: Partial<TextElement>): void {
  if (!activeElement.value || activeElement.value.type !== 'text') {
    return
  }

  updateElement(activeElement.value.id, (item) => {
    if (item.type !== 'text') {
      return item
    }

    return {
      ...item,
      ...patch,
    }
  })
}

function updateActiveImage(patch: Partial<ImageElement>): void {
  if (!activeElement.value || activeElement.value.type !== 'image') {
    return
  }

  updateElement(activeElement.value.id, (item) => {
    if (item.type !== 'image') {
      return item
    }

    return {
      ...item,
      ...patch,
    }
  })
}

function removeActiveElement(): void {
  if (!activeElementId.value) {
    return
  }

  customElements.value = customElements.value.filter((item) => item.id !== activeElementId.value)
  activeElementId.value = null
}

function duplicateActiveElement(): void {
  if (!activeElement.value) {
    return
  }

  const id = createElementId()

  if (activeElement.value.type === 'text') {
    const clone: TextElement = {
      ...activeElement.value,
      id,
      x: activeElement.value.x + 18,
      y: activeElement.value.y + 18,
    }

    customElements.value = [...customElements.value, clone]
    activeElementId.value = id

    return
  }

  const clone: ImageElement = {
    ...activeElement.value,
    id,
    x: activeElement.value.x + 18,
    y: activeElement.value.y + 18,
  }

  customElements.value = [...customElements.value, clone]
  activeElementId.value = id
}

async function removeActiveImageBackground(): Promise<void> {
  if (!activeElement.value || activeElement.value.type !== 'image') {
    return
  }

  const image = await loadImage(activeElement.value.src)

  if (!image) {
    showToast('Gambar tidak ditemukan.')

    return
  }

  const canvas = document.createElement('canvas')
  canvas.width = image.naturalWidth
  canvas.height = image.naturalHeight

  const ctx = canvas.getContext('2d', { willReadFrequently: true })

  if (!ctx) {
    return
  }

  ctx.drawImage(image, 0, 0)
  const data = ctx.getImageData(0, 0, canvas.width, canvas.height)

  for (let i = 0; i < data.data.length; i += 4) {
    const r = data.data[i]
    const g = data.data[i + 1]
    const b = data.data[i + 2]

    if (r > 220 && g > 220 && b > 220) {
      data.data[i + 3] = 0
    }
  }

  ctx.putImageData(data, 0, 0)
  const nextSrc = canvas.toDataURL('image/png')
  const nextImg = await loadImage(nextSrc)

  updateActiveImage({
    src: nextSrc,
    naturalWidth: nextImg?.naturalWidth ?? activeElement.value.naturalWidth,
    naturalHeight: nextImg?.naturalHeight ?? activeElement.value.naturalHeight,
  })

  showToast('Background putih gambar dihapus.')
}

async function addActiveImageOutline(): Promise<void> {
  if (!activeElement.value || activeElement.value.type !== 'image') {
    return
  }

  const image = await loadImage(activeElement.value.src)

  if (!image) {
    showToast('Gambar tidak ditemukan.')

    return
  }

  const thickness = 4
  const canvas = document.createElement('canvas')
  canvas.width = image.naturalWidth + thickness * 2
  canvas.height = image.naturalHeight + thickness * 2

  const ctx = canvas.getContext('2d')

  if (!ctx) {
    return
  }

  const offsets: Array<[number, number]> = [
    [-1, -1],
    [0, -1],
    [1, -1],
    [-1, 0],
    [1, 0],
    [-1, 1],
    [0, 1],
    [1, 1],
  ]

  for (const [ox, oy] of offsets) {
    ctx.drawImage(
      image,
      thickness + ox * thickness,
      thickness + oy * thickness,
      image.naturalWidth,
      image.naturalHeight,
    )
  }

  ctx.globalCompositeOperation = 'source-in'
  ctx.fillStyle = '#ffffff'
  ctx.fillRect(0, 0, canvas.width, canvas.height)
  ctx.globalCompositeOperation = 'source-over'
  ctx.drawImage(image, thickness, thickness, image.naturalWidth, image.naturalHeight)

  const nextSrc = canvas.toDataURL('image/png')
  const nextImg = await loadImage(nextSrc)

  updateActiveImage({
    src: nextSrc,
    naturalWidth: nextImg?.naturalWidth ?? activeElement.value.naturalWidth,
    naturalHeight: nextImg?.naturalHeight ?? activeElement.value.naturalHeight,
  })

  showToast('Outline putih berhasil ditambahkan.')
}

function updateLayerColor(id: LayerId, color: string): void {
  layerColors.value = {
    ...layerColors.value,
    [id]: color,
  }
}

function toggleLayerOutline(id: LayerId, enabled: boolean): void {
  const current = layerOutlines.value[id] ?? {
    active: false,
    color: '#000000',
    size: 2,
  }

  layerOutlines.value = {
    ...layerOutlines.value,
    [id]: {
      ...current,
      active: enabled,
    },
  }
}

function updateLayerOutlineColor(id: LayerId, color: string): void {
  const current = layerOutlines.value[id] ?? {
    active: false,
    color: '#000000',
    size: 2,
  }

  layerOutlines.value = {
    ...layerOutlines.value,
    [id]: {
      ...current,
      color,
    },
  }
}

function updateLayerOutlineSize(id: LayerId, size: number): void {
  const current = layerOutlines.value[id] ?? {
    active: false,
    color: '#000000',
    size: 2,
  }

  layerOutlines.value = {
    ...layerOutlines.value,
    [id]: {
      ...current,
      size: Number.isFinite(size) ? Math.max(1, Math.min(50, size)) : 2,
    },
  }
}

function getTextElementStyle(item: TextElement): CSSProperties {
  const scaleX = item.scaleX ?? 1
  const scaleY = item.scaleY ?? 1

  const style: CSSProperties = {
    transform: `translate(${item.x * scale.value}px, ${item.y * scale.value}px) rotate(${item.rotation}deg) scale(${scaleX}, ${scaleY})`,
    transformOrigin: 'top left',
    fontSize: `${item.size * scale.value}px`,
    fontFamily: 'Hanken Grotesk, sans-serif',
    color: item.color,
    lineHeight: '1',
    fontWeight: '900',
  }

  ;(style as Record<string, string>).WebkitTextStroke = `${item.strokeSize * scale.value}px ${item.strokeColor}`

  return style
}

function getImageElementStyle(item: ImageElement): CSSProperties {
  const ratio = item.naturalHeight / Math.max(1, item.naturalWidth)
  const scaleX = item.scaleX ?? 1
  const scaleY = item.scaleY ?? 1

  return {
    transform: `translate(${item.x * scale.value}px, ${item.y * scale.value}px) rotate(${item.rotation}deg) scale(${scaleX}, ${scaleY})`,
    transformOrigin: 'top left',
    width: `${item.size * scale.value}px`,
    height: `${item.size * ratio * scale.value}px`,
    opacity: String(item.opacity),
    filter: 'drop-shadow(0 3px 8px rgba(15, 23, 42, 0.24))',
  }
}

function getActiveElementHandleStyle(item: CustomElement): CSSProperties {
  const dims = getElementScaledSize(item)

  return {
    transform: `translate(${item.x * scale.value}px, ${item.y * scale.value}px) rotate(${item.rotation}deg)`,
    width: `${dims.width * scale.value}px`,
    height: `${dims.height * scale.value}px`,
    transformOrigin: 'top left',
    boxShadow: '0 0 0 1px rgba(18, 94, 64, 0.32)',
    background: 'rgba(124, 140, 90, 0.04)',
  }
}

function handleViewerKeydown(event: KeyboardEvent): void {
  if (event.key === 'Escape') {
    activeElementId.value = null
  }
}

function onLayerPickerChange(event: Event): void {
  if (activeLayerPickId.value === null) {
    return
  }

  const color = (event.target as HTMLInputElement).value
  updateLayerColor(activeLayerPickId.value, color)
}

function onLayerColorInput(id: LayerId, event: Event): void {
  const color = (event.target as HTMLInputElement).value
  updateLayerColor(id, color)
}

function onLayerOutlineToggleInput(id: LayerId, event: Event): void {
  const enabled = (event.target as HTMLInputElement).checked
  toggleLayerOutline(id, enabled)
}

function onLayerOutlineColorInput(id: LayerId, event: Event): void {
  const color = (event.target as HTMLInputElement).value
  updateLayerOutlineColor(id, color)
}

function onLayerOutlineSizeInput(id: LayerId, event: Event): void {
  const size = Number((event.target as HTMLInputElement).value)
  updateLayerOutlineSize(id, size)
}

function handleViewerClick(event: MouseEvent): void {
  if (pointerMovedRef.value) {
    pointerMovedRef.value = false

    return
  }

  const target = event.target as HTMLElement

  if (target.closest('[data-transform-handle="true"]')) {
    return
  }

  if (target.closest('[data-draggable="true"]')) {
    return
  }

  activeElementId.value = null

  const viewer = viewerRef.value

  if (!viewer) {
    return
  }

  const rect = viewer.getBoundingClientRect()
  const x = ((event.clientX - rect.left) * CANVAS_SIZE) / rect.width
  const y = ((event.clientY - rect.top) * CANVAS_SIZE) / rect.height

  const hitCanvas = document.createElement('canvas')
  hitCanvas.width = 1
  hitCanvas.height = 1
  const hitCtx = hitCanvas.getContext('2d', { willReadFrequently: true })

  if (!hitCtx) {
    return
  }

  const reversedIds = [...layerIds.value].sort((a, b) => b - a)

  for (const id of reversedIds) {
    const image = layerImagesRef.value.get(id)

    if (!image) {
      continue
    }

    hitCtx.clearRect(0, 0, 1, 1)
    hitCtx.drawImage(image, -x, PREVIEW_OFFSET_Y - y, CANVAS_SIZE, CANVAS_SIZE)
    const alpha = hitCtx.getImageData(0, 0, 1, 1).data[3]

    if (alpha > 20) {
      activeLayerPickId.value = id

      const picker = colorPickerRef.value

      if (picker) {
        picker.value = layerColors.value[id] ?? '#ffffff'
        picker.click()
      }

      showToast(`Aksen ${id} dipilih.`)

      return
    }
  }
}

function handlePointerMove(event: PointerEvent): void {
  const drag = dragRef.value
  const viewer = viewerRef.value

  if (!drag || !viewer) {
    return
  }

  const rect = viewer.getBoundingClientRect()
  const dx = ((event.clientX - drag.startClientX) * CANVAS_SIZE) / rect.width
  const dy = ((event.clientY - drag.startClientY) * CANVAS_SIZE) / rect.height

  if (Math.abs(dx) > 0.3 || Math.abs(dy) > 0.3) {
    pointerMovedRef.value = true
  }

  customElements.value = customElements.value.map((item) => {
    if (item.id !== drag.id) {
      return item
    }

    return {
      ...item,
      x: drag.initX + dx,
      y: drag.initY + dy,
    }
  })
}

function handlePointerUp(): void {
  dragRef.value = null
  window.removeEventListener('pointermove', handlePointerMove)
  window.removeEventListener('pointerup', handlePointerUp)
}

function startDragElement(event: PointerEvent, id: string): void {
  event.preventDefault()
  event.stopPropagation()
  stopTransformInteraction()

  const item = customElements.value.find((element) => element.id === id)

  if (!item) {
    return
  }

  activeElementId.value = id
  pointerMovedRef.value = false

  dragRef.value = {
    id,
    startClientX: event.clientX,
    startClientY: event.clientY,
    initX: item.x,
    initY: item.y,
  }

  window.addEventListener('pointermove', handlePointerMove)
  window.addEventListener('pointerup', handlePointerUp)
}

function beginTransformInteraction(event: PointerEvent, id: string, mode: 'scale' | 'rotate'): void {
  const viewer = viewerRef.value
  const element = customElements.value.find((item) => item.id === id)

  if (!viewer || !element) {
    return
  }

  pointerMovedRef.value = true
  activeElementId.value = id

  const rect = viewer.getBoundingClientRect()
  const dims = getElementScaledSize(element)
  const centerCanvasX = element.x + dims.width / 2
  const centerCanvasY = element.y + dims.height / 2
  const centerClientX = rect.left + (centerCanvasX * rect.width) / CANVAS_SIZE
  const centerClientY = rect.top + (centerCanvasY * rect.height) / CANVAS_SIZE
  const dx = event.clientX - centerClientX
  const dy = event.clientY - centerClientY

  transformRef.value = {
    id,
    mode,
    centerClientX,
    centerClientY,
    startDistance: Math.max(1, Math.hypot(dx, dy)),
    startAngle: Math.atan2(dy, dx),
    initialScaleX: element.scaleX ?? 1,
    initialScaleY: element.scaleY ?? 1,
    initialRotation: element.rotation,
  }

  window.addEventListener('pointermove', handleTransformPointerMove)
  window.addEventListener('pointerup', stopTransformInteraction)
}

function handleTransformPointerMove(event: PointerEvent): void {
  const state = transformRef.value

  if (!state) {
    return
  }

  const dx = event.clientX - state.centerClientX
  const dy = event.clientY - state.centerClientY

  if (state.mode === 'scale') {
    const ratio = clampValue(Math.hypot(dx, dy) / state.startDistance, 0.2, 3.2)
    const nextScaleX = clampValue(state.initialScaleX * ratio, 0.2, 3)
    const nextScaleY = clampValue(state.initialScaleY * ratio, 0.2, 3)

    updateElement(state.id, (item) => {
      return {
        ...item,
        scaleX: nextScaleX,
        scaleY: nextScaleY,
      }
    })

    return
  }

  const angle = Math.atan2(dy, dx)
  let delta = ((angle - state.startAngle) * 180) / Math.PI

  if (delta > 180) {
    delta -= 360
  }

  if (delta < -180) {
    delta += 360
  }

  updateElement(state.id, (item) => {
    return {
      ...item,
      rotation: state.initialRotation + delta,
    }
  })
}

function stopTransformInteraction(): void {
  transformRef.value = null
  window.removeEventListener('pointermove', handleTransformPointerMove)
  window.removeEventListener('pointerup', stopTransformInteraction)
}

function startScaleTransform(event: PointerEvent, id: string): void {
  beginTransformInteraction(event, id, 'scale')
}

function startRotateTransform(event: PointerEvent, id: string): void {
  beginTransformInteraction(event, id, 'rotate')
}

function onTextInput(event: Event): void {
  updateActiveText({ text: (event.target as HTMLTextAreaElement).value })
}

function onTextColorInput(event: Event): void {
  updateActiveText({ color: (event.target as HTMLInputElement).value })
}

function onTextStrokeColorInput(event: Event): void {
  updateActiveText({ strokeColor: (event.target as HTMLInputElement).value })
}

function onTextStrokeSizeInput(event: Event): void {
  updateActiveText({ strokeSize: Math.max(0, Number((event.target as HTMLInputElement).value)) })
}

function onImageOpacityInput(event: Event): void {
  const opacity = Number((event.target as HTMLInputElement).value)
  updateActiveImage({ opacity: Number.isFinite(opacity) ? Math.max(0.1, Math.min(1, opacity)) : 1 })
}

function clampValue(value: number, min: number, max: number): number {
  return Math.max(min, Math.min(max, value))
}

function updateActiveTransform(patch: Partial<ElementBase>): void {
  if (!activeElement.value) {
    return
  }

  if (activeElement.value.type === 'text') {
    updateActiveText(patch)

    return
  }

  updateActiveImage(patch)
}

function resetActiveTransform(): void {
  if (!activeElement.value) {
    return
  }

  updateActiveTransform({
    rotation: 0,
    scaleX: 1,
    scaleY: 1,
  })
}

async function drawElementsToCanvas(
  ctx: CanvasRenderingContext2D,
  targetWidth: number,
  targetHeight: number,
  mirror: boolean,
): Promise<void> {
  const ratioX = targetWidth / CANVAS_SIZE
  const ratioY = targetHeight / CANVAS_SIZE

  const getMirrorTopLeft = (
    x: number,
    y: number,
    width: number,
    height: number,
    rotation: number,
    scaleX: number,
    scaleY: number,
  ): { x: number; y: number; rotation: number } => {
    const mirroredRotation = 180 - rotation
    const theta = (mirroredRotation * Math.PI) / 180
    const centerX = x + width / 2
    const centerY = y + height / 2
    const mirroredCenterY = targetHeight - centerY

    const vx = -(width / 2) * scaleX
    const vy = -(height / 2) * scaleY
    const offsetX = vx * Math.cos(theta) - vy * Math.sin(theta)
    const offsetY = vx * Math.sin(theta) + vy * Math.cos(theta)

    return {
      x: centerX + offsetX,
      y: mirroredCenterY + offsetY,
      rotation: mirroredRotation,
    }
  }

  for (const element of customElements.value) {
    if (element.type === 'image') {
      const image = await loadImage(element.src)

      if (!image) {
        continue
      }

      const imageHeight = element.size * (element.naturalHeight / Math.max(1, element.naturalWidth))
      const drawW = element.size * ratioX
      const drawH = imageHeight * ratioY
      const x = element.x * ratioX
      const y = element.y * ratioY
      const sx = element.scaleX ?? 1
      const sy = element.scaleY ?? 1

      const drawImageAt = (xPos: number, yPos: number, rotationDeg: number): void => {
        ctx.save()
        ctx.globalAlpha = element.opacity
        ctx.translate(xPos, yPos)
        ctx.rotate((rotationDeg * Math.PI) / 180)
        ctx.scale(sx, sy)
        ctx.drawImage(image, 0, 0, drawW, drawH)
        ctx.restore()
      }

      drawImageAt(x, y, element.rotation)

      if (mirror) {
        const mirrored = getMirrorTopLeft(x, y, drawW, drawH, element.rotation, sx, sy)
        drawImageAt(mirrored.x, mirrored.y, mirrored.rotation)
      }

      continue
    }

    const fontSize = element.size * ratioX
    const drawW = getTextDims(ctx, element.text, fontSize).width
    const drawH = fontSize
    const x = element.x * ratioX
    const y = element.y * ratioY
    const sx = element.scaleX ?? 1
    const sy = element.scaleY ?? 1

    const drawTextAt = (xPos: number, yPos: number, rotationDeg: number): void => {
      ctx.save()
      ctx.translate(xPos, yPos)
      ctx.rotate((rotationDeg * Math.PI) / 180)
      ctx.scale(sx, sy)
      ctx.font = `900 ${fontSize}px Hanken Grotesk, sans-serif`
      ctx.textAlign = 'left'
      ctx.textBaseline = 'top'

      if (element.strokeSize > 0) {
        ctx.lineWidth = Math.max(0.5, element.strokeSize * ratioX)
        ctx.strokeStyle = element.strokeColor
        ctx.lineJoin = 'round'
        ctx.miterLimit = 2
        ctx.strokeText(element.text, 0, 0)
      }

      ctx.fillStyle = element.color
      ctx.fillText(element.text, 0, 0)
      ctx.restore()
    }

    drawTextAt(x, y, element.rotation)

    if (mirror) {
      const mirrored = getMirrorTopLeft(x, y, drawW, drawH, element.rotation, sx, sy)
      drawTextAt(mirrored.x, mirrored.y, mirrored.rotation)
    }
  }
}

async function createPreviewExportCanvas(): Promise<HTMLCanvasElement> {
  const exportCanvas = document.createElement('canvas')
  exportCanvas.width = CANVAS_SIZE
  exportCanvas.height = CANVAS_SIZE

  const ctx = exportCanvas.getContext('2d')

  if (!ctx) {
    return exportCanvas
  }

  if (previewCanvasRef.value) {
    ctx.drawImage(previewCanvasRef.value, 0, 0)
  }

  await drawElementsToCanvas(ctx, CANVAS_SIZE, CANVAS_SIZE, false)

  const rowOne = `ID/WA: ${phone.value || '-'} | Nama: ${name.value || '-'} | Size: ${shoeSize.value || '-'}`
  const rowTwo = `O: ${selectedOutsoleShort.value} | M: ${selectedMidsoleShort.value} | I: ${selectedInsoleShort.value}`

  ctx.save()
  ctx.fillStyle = 'rgba(255, 255, 255, 0.92)'
  ctx.fillRect(16, CANVAS_SIZE - 92, 520, 64)
  ctx.strokeStyle = '#7c8c5a'
  ctx.lineWidth = 3
  ctx.strokeRect(16, CANVAS_SIZE - 92, 520, 64)

  ctx.font = '600 16px Hanken Grotesk, sans-serif'
  ctx.fillStyle = '#1a1a1a'
  ctx.fillText(rowOne, 26, CANVAS_SIZE - 64)
  ctx.fillText(rowTwo, 26, CANVAS_SIZE - 38)
  ctx.restore()

  return exportCanvas
}

async function createPatternCanvas(): Promise<{ canvas: HTMLCanvasElement; watermarkLabel: string }> {
  const folder = activeFolder.value
  const model = currentModelMeta.value

  const canvas = document.createElement('canvas')
  canvas.width = 2048
  canvas.height = 2048

  if (!folder || !model) {
    return {
      canvas,
      watermarkLabel: 'POLA_UNKNOWN',
    }
  }

  const patternBase = patternBaseFileRef.value
    ? await loadImage(buildAssetUrl(folder.key, model.id, patternBaseFileRef.value))
    : null

  const patternImages = new Map<LayerId, HTMLImageElement>()

  for (const id of layerIds.value) {
    const fileName = patternLayerFilesRef.value.get(id)

    if (fileName) {
      const image = await loadImage(buildAssetUrl(folder.key, model.id, fileName))

      if (image) {
        patternImages.set(id, image)
        continue
      }
    }

    const fallback = layerImagesRef.value.get(id)

    if (fallback) {
      patternImages.set(id, fallback)
    }
  }

  let targetWidth = patternBase?.naturalWidth ?? 2048
  let targetHeight = patternBase?.naturalHeight ?? 2048

  for (const image of patternImages.values()) {
    targetWidth = Math.max(targetWidth, image.naturalWidth)
    targetHeight = Math.max(targetHeight, image.naturalHeight)
  }

  canvas.width = targetWidth
  canvas.height = targetHeight

  const ctx = canvas.getContext('2d')

  if (!ctx) {
    return {
      canvas,
      watermarkLabel: 'POLA_UNKNOWN',
    }
  }

  ctx.clearRect(0, 0, targetWidth, targetHeight)

  if (patternBase) {
    const cutlineOutline = drawOutlineLayer(patternBase, '#000000', 15, targetWidth, targetHeight)
    ctx.drawImage(cutlineOutline, 0, 0)
  }

  const ratioX = targetWidth / CANVAS_SIZE

  for (const id of layerIds.value) {
    const image = patternImages.get(id)

    if (!image) {
      continue
    }

    const outline = layerOutlines.value[id]

    if (outline?.active) {
      const outlineCanvas = drawOutlineLayer(image, outline.color, outline.size * ratioX, targetWidth, targetHeight)
      ctx.drawImage(outlineCanvas, 0, 0)
    }

    const fillCanvas = drawFilledLayer(image, layerColors.value[id] ?? '#ffffff', targetWidth, targetHeight)
    ctx.drawImage(fillCanvas, 0, 0)
  }

  await drawElementsToCanvas(ctx, targetWidth, targetHeight, true)

  const phoneLast4 = phone.value.replace(/\D/g, '').slice(-4) || '0000'
  const safeName = sanitizeFileToken(name.value || 'NONAME')
  const safeSize = sanitizeFileToken(shoeSize.value || 'NOSIZE')
  const safeOutsole = sanitizeFileToken(selectedOutsoleShort.value)
  const safeMidsole = sanitizeFileToken(selectedMidsoleShort.value)
  const safeInsole = sanitizeFileToken(selectedInsoleShort.value)
  const fastTrackTag = fastTrackEnabled.value ? 'FAST' : 'REG'
  const boxTag = customBoxEnabled.value ? 'BOX' : 'STD'
  const watermarkLabel = `POLA_${safeName}_WA${phoneLast4}_SZ${safeSize}_${safeOutsole}_${safeMidsole}_${safeInsole}_${fastTrackTag}_${boxTag}`

  ctx.save()
  const labelFontSize = Math.max(30, targetHeight * 0.015)
  const wmX = Math.min(WATERMARK_X, targetWidth * 0.6)
  const wmY = Math.min(WATERMARK_Y, targetHeight * 0.82)

  ctx.font = `800 ${labelFontSize}px Hanken Grotesk, sans-serif`
  ctx.textAlign = 'center'
  ctx.textBaseline = 'middle'
  ctx.translate(wmX, wmY)
  ctx.rotate((WATERMARK_ANGLE * Math.PI) / 180)

  ctx.lineWidth = 8
  ctx.strokeStyle = '#000000'
  ctx.strokeText(watermarkLabel, 0, 0)
  ctx.fillStyle = '#ffffff'
  ctx.fillText(watermarkLabel, 0, 0)
  ctx.restore()

  return {
    canvas,
    watermarkLabel,
  }
}

function downloadCanvas(canvas: HTMLCanvasElement, fileName: string): void {
  canvas.toBlob((blob) => {
    if (!blob) {
      return
    }

    const href = URL.createObjectURL(blob)
    const anchor = document.createElement('a')
    anchor.download = fileName
    anchor.href = href
    anchor.click()

    window.setTimeout(() => {
      URL.revokeObjectURL(href)
    }, 1200)
  }, 'image/png')
}

async function handleSave(): Promise<void> {
  if (!selectedOutsole.value || !selectedMidsole.value || !selectedInsole.value) {
    currentStep.value = 1
    window.alert('Mohon pilih material outsole, midsole, dan insole sebelum checkout.')

    return
  }

  if (!name.value || !phone.value || !guestEmail.value || !guestAddress.value || !shoeSize.value) {
    currentStep.value = TOTAL_STEPS
    window.alert('Mohon isi data wajib guest checkout: nama, WhatsApp, email, ukuran, dan alamat.')

    return
  }

  isSaving.value = true

  try {
    const preview = await createPreviewExportCanvas()
    const pattern = await createPatternCanvas()

    const phoneLast4 = phone.value.replace(/\D/g, '').slice(-4) || '0000'
    const safeName = sanitizeFileToken(name.value)
    const safeSize = sanitizeFileToken(shoeSize.value)
    const fastTrackTag = fastTrackEnabled.value ? 'FAST' : 'REG'
    const boxTag = customBoxEnabled.value ? 'BOX' : 'STD'

    const previewName = `PREVIEW_${safeName}_WA${phoneLast4}_SZ${safeSize}_${fastTrackTag}_${boxTag}.png`
    const patternName = `${pattern.watermarkLabel}.png`

    downloadCanvas(preview, previewName)
    downloadCanvas(pattern.canvas, patternName)

    const waTarget = normalizePhoneForWa(phone.value)
    const waMessage = encodeURIComponent([
      WHATSAPP_INTRO,
      `Nama: ${name.value}`,
      `WA: ${phone.value}`,
      `Email: ${guestEmail.value}`,
      `Ukuran: ${shoeSize.value}`,
      `Outsole: ${selectedOutsoleMeta.value?.title ?? '-'}`,
      `Midsole: ${selectedMidsoleMeta.value?.title ?? '-'}`,
      `Insole: ${selectedInsoleMeta.value?.title ?? '-'}`,
      `Fast Track: ${fastTrackEnabled.value ? `Ya (+${formatCurrency(FAST_TRACK_FEE)})` : 'Tidak'}`,
      `Custom Box: ${customBoxEnabled.value ? `Ya (+${formatCurrency(CUSTOM_BOX_FEE)})` : 'Tidak'}`,
      `Total Estimasi: ${formatCurrency(guestCheckoutTotal.value)}`,
      `Alamat: ${guestAddress.value}`,
      `Catatan: ${guestNotes.value || '-'}`,
    ].join('\n'))

    waLink.value = `https://wa.me/${waTarget}?text=${waMessage}`
    showToast('Guest checkout berhasil diproses. File preview dan pola telah diunduh.')
  } catch (error) {
    console.error('Failed to save design:', error)
    window.alert('Terjadi kesalahan saat memproses desain. Silakan coba lagi.')
  } finally {
    isSaving.value = false
  }
}

async function refreshCatalogAndAssets(): Promise<void> {
  await fetchCatalog(true)

  if (lazyCanvasReady.value) {
    await loadModelAssets(true)
  }
}

watch(
  [layerColors, layerOutlines, layerIds, assetRevision],
  () => {
    scheduleDraw()
  },
  {
    deep: true,
  },
)

watch(
  [activeFolderKey, currentModel, lazyCanvasReady],
  ([folderKey, modelId, lazy]) => {
    if (!folderKey || modelId === null || !lazy) {
      return
    }

    void loadModelAssets(false)
  },
  {
    immediate: true,
  },
)

watch(activeFolderKey, (nextFolder) => {
  const folder = catalogFolders.value.find((item) => item.key === nextFolder)

  if (!folder) {
    currentModel.value = null

    return
  }

  if (!folder.models.some((item) => item.id === currentModel.value)) {
    currentModel.value = folder.models[0]?.id ?? null
  }
})

onMounted(() => {
  void fetchCatalog(false)

  const viewer = viewerRef.value

  if (!viewer) {
    lazyCanvasReady.value = true

    return
  }

  viewerWidth.value = Math.max(320, viewer.clientWidth)

  resizeObserverRef.value = new ResizeObserver((entries) => {
    const width = entries[0]?.contentRect.width

    if (width && width > 0) {
      viewerWidth.value = width
    }
  })

  resizeObserverRef.value.observe(viewer)

  if ('IntersectionObserver' in window) {
    viewerObserverRef.value = new IntersectionObserver(
      (entries) => {
        if (entries[0]?.isIntersecting) {
          lazyCanvasReady.value = true
          viewerObserverRef.value?.disconnect()
          viewerObserverRef.value = null
        }
      },
      { rootMargin: '220px' },
    )

    viewerObserverRef.value.observe(viewer)
  } else {
    lazyCanvasReady.value = true
  }
})

onUnmounted(() => {
  if (drawFrameRef.value !== null) {
    window.cancelAnimationFrame(drawFrameRef.value)
  }

  if (toastTimerRef.value !== null) {
    window.clearTimeout(toastTimerRef.value)
  }

  window.removeEventListener('pointermove', handlePointerMove)
  window.removeEventListener('pointerup', handlePointerUp)
  window.removeEventListener('pointermove', handleTransformPointerMove)
  window.removeEventListener('pointerup', stopTransformInteraction)

  resizeObserverRef.value?.disconnect()
  viewerObserverRef.value?.disconnect()

  for (const src of uploadObjectUrlsRef.value.values()) {
    URL.revokeObjectURL(src)
  }

  uploadObjectUrlsRef.value.clear()
})
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.pattern-wave {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='60' viewBox='0 0 120 60'%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.16' d='M0 30 Q 15 15 30 30 T 60 30 T 90 30 T 120 30'/%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.6' opacity='0.11' d='M0 40 Q 15 25 30 40 T 60 40 T 90 40 T 120 40'/%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.5' opacity='0.08' d='M0 50 Q 15 35 30 50 T 60 50 T 90 50 T 120 50'/%3E%3C/svg%3E");
  background-size: 120px 60px;
}

.pattern-grid {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='50' height='50' viewBox='0 0 50 50'%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='0.6' opacity='0.16' d='M0 0h50v50H0z M25 0v50 M0 25h50' /%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='0.4' opacity='0.09' d='M10 10 L40 40 M40 10 L10 40' /%3E%3C/svg%3E");
  background-size: 50px 50px;
}

.vertical-text {
  writing-mode: vertical-rl;
  text-orientation: mixed;
}

.card-lift {
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow:
    0 2px 10px rgba(20, 20, 20, 0.08),
    inset 0 1px 0 rgba(255, 255, 255, 0.28);
}

.card-lift:hover {
  transform: translateY(-4px);
  box-shadow:
    0 14px 24px rgba(20, 20, 20, 0.13),
    inset 0 1px 0 rgba(255, 255, 255, 0.32);
}

.control-icon-btn {
  border: 1px solid rgba(20, 20, 20, 0.15);
  border-radius: 0.65rem;
  background: #f6f5f0;
  padding: 0.5rem 0.25rem;
  font-size: 0.65rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  color: #3a3a3a;
  transition: all 0.18s ease;
}

.control-icon-btn:hover {
  border-color: rgba(20, 20, 20, 0.28);
  color: #111;
  background: #ece9de;
}

.control-action-btn {
  border: 1px solid rgba(20, 20, 20, 0.16);
  border-radius: 0.7rem;
  background: #fff;
  padding: 0.55rem 0.5rem;
  font-size: 0.68rem;
  font-weight: 900;
  letter-spacing: 0.08em;
  color: #3a3a3a;
  transition: all 0.18s ease;
}

.control-action-btn:hover {
  border-color: rgba(20, 20, 20, 0.34);
  background: #f4f4ef;
  color: #111;
}

.transform-handle {
  position: absolute;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 0;
  border-radius: 999px;
  background: #125e40;
  color: #f6f5f0;
  font-size: 0.72rem;
  font-weight: 900;
  line-height: 1;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.22);
  transition: transform 0.15s ease, background 0.15s ease;
}

.transform-handle:hover {
  background: #0d4a32;
}

.transform-handle-rotate {
  top: -1.95rem;
  left: 50%;
  width: 1.65rem;
  height: 1.65rem;
  transform: translateX(-50%);
  cursor: grab;
}

.transform-handle-rotate:active {
  cursor: grabbing;
  transform: translateX(-50%) scale(0.94);
}

.transform-handle-scale {
  right: -0.78rem;
  bottom: -0.78rem;
  width: 1.55rem;
  height: 1.55rem;
  cursor: nwse-resize;
}

.transform-handle-scale:active {
  transform: scale(0.94);
}

.transform-handle-connector {
  position: absolute;
  top: -1.15rem;
  left: 50%;
  width: 2px;
  height: 1.15rem;
  transform: translateX(-50%);
  background: rgba(18, 94, 64, 0.72);
}

.editor-scroll::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.editor-scroll::-webkit-scrollbar-track {
  background: rgba(26, 26, 26, 0.08);
  border-radius: 999px;
}

.editor-scroll::-webkit-scrollbar-thumb {
  background: rgba(26, 26, 26, 0.36);
  border-radius: 999px;
}

@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(24px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
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

.animate-slide-up {
  animation: slideInUp 0.75s ease-out forwards;
}

.animate-pulse-soft {
  animation: pulse-soft 2s ease-in-out infinite;
}

.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: all 0.2s ease;
}

.toast-fade-enter-from,
.toast-fade-leave-to {
  opacity: 0;
  transform: translate(-50%, 8px);
}

@media (max-width: 1024px) {
  .vertical-text {
    display: none;
  }
}
</style>
