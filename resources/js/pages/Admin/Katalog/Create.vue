<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { katalog } from '@/routes/admin';

const form = useForm({
    shoe_model_id: null as number | null,
    name: '',
    collection: '',
    price: 0,
    status: 'ready' as 'ready' | 'po' | 'habis',
    description: '',
    min_size: '' as number | string,
    max_size: '' as number | string,
    is_active: true,
    thumbnail: null as File | null,
    images: [] as File[],
});

defineProps<{
    shoeModels: Array<{ id: number; name: string }>;
}>();

const formattedPrice = computed({
    get: () => new Intl.NumberFormat('id-ID').format(form.price),
    set: (val: string) => {
        const numericValue = parseInt(val.replace(/\D/g, '')) || 0;
        form.price = numericValue;
    },
});

const thumbnailPreview = ref<string | null>(null);
const detailPreviews = ref<string[]>([]);

const handleThumbnailUpload = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    
    if (file) {
        form.thumbnail = file;
        thumbnailPreview.value = URL.createObjectURL(file);
    }
};

const handleDetailsUpload = (event: Event) => {
    const files = (event.target as HTMLInputElement).files;

    if (files) {
        const fileList = Array.from(files);
        // Limit to 6 total
        const remainingSlots = 6 - form.images.length;
        const newFiles = fileList.slice(0, remainingSlots);

        form.images = [...form.images, ...newFiles];
        detailPreviews.value = form.images.map((file) =>
            URL.createObjectURL(file),
        );
    }
};

const removeDetailImage = (index: number) => {
    form.images.splice(index, 1);
    detailPreviews.value.splice(index, 1);
};

const submit = () => {
    form.post(route('admin.katalog.store'), {
        forceFormData: true,
        onSuccess: () => {
            // handle success
        },
    });
};
</script>

<template>
    <Head title="Tambah Produk Baru" />

    <AdminLayout>
        <template #header> Tambah Produk Baru </template>

        <form
            @submit.prevent="submit"
            class="mx-auto max-w-300 px-4 pt-6 pb-20"
        >
            <!-- Back Button -->
            <div class="mb-6">
                <Link
                    :href="katalog.url()"
                    class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition-colors hover:text-indigo-600"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7"
                        />
                    </svg>
                    <span>Kembali ke Katalog</span>
                </Link>
            </div>

            <div class="flex flex-col gap-8 lg:flex-row">
                <!-- KOLOM KIRI: Detail Utama & Media -->
                <div class="flex-1 space-y-8">
                    <!-- Section: Media -->
                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <h3
                            class="mb-6 font-['Montserrat'] text-lg font-bold text-slate-800"
                        >
                            Media Produk
                        </h3>

                        <div class="grid gap-6 md:grid-cols-3">
                            <!-- Thumbnail (Main) -->
                            <div class="md:col-span-1">
                                <label
                                    class="mb-2 block text-xs font-bold tracking-wider text-slate-500 uppercase"
                                    >Thumbnail</label
                                >
                                <div class="relative aspect-square">
                                    <div
                                        class="group relative h-full w-full overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 transition-all hover:border-indigo-400"
                                    >
                                        <img
                                            v-if="thumbnailPreview"
                                            :src="thumbnailPreview"
                                            class="h-full w-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex h-full w-full flex-col items-center justify-center p-4 text-center"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="mb-2 h-8 w-8 text-slate-300"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                            <span
                                                class="text-[10px] font-bold text-slate-400 uppercase"
                                                >Upload Utama</span
                                            >
                                        </div>
                                        <input
                                            type="file"
                                            accept="image/*"
                                            @change="handleThumbnailUpload"
                                            class="absolute inset-0 cursor-pointer opacity-0"
                                        />
                                    </div>
                                    <button
                                        v-if="thumbnailPreview"
                                        type="button"
                                        @click="
                                            thumbnailPreview = null;
                                            form.thumbnail = null;
                                        "
                                        class="absolute -top-2 -right-2 flex h-7 w-7 items-center justify-center rounded-full bg-rose-500 text-white shadow-md"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Gallery (Details) -->
                            <div class="md:col-span-2">
                                <label
                                    class="mb-2 block text-xs font-bold tracking-wider text-slate-500 uppercase"
                                    >Galeri Detail ({{
                                        detailPreviews.length
                                    }}/6)</label
                                >
                                <div
                                    class="grid grid-cols-3 gap-3 md:grid-cols-4"
                                >
                                    <div
                                        v-for="(preview, idx) in detailPreviews"
                                        :key="idx"
                                        class="group relative aspect-square overflow-hidden rounded-xl border border-slate-200 shadow-sm"
                                    >
                                        <img
                                            :src="preview"
                                            class="h-full w-full object-cover"
                                        />
                                        <button
                                            @click="removeDetailImage(idx)"
                                            type="button"
                                            class="absolute inset-0 flex items-center justify-center bg-rose-500/80 text-white opacity-0 transition-opacity group-hover:opacity-100"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2.5"
                                                    d="M6 18L18 6M6 6l12 12"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                    <div
                                        v-if="detailPreviews.length < 6"
                                        class="relative flex aspect-square cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 transition-all hover:border-indigo-400 hover:bg-white"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-slate-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 4v16m8-8H4"
                                            />
                                        </svg>
                                        <input
                                            type="file"
                                            multiple
                                            accept="image/*"
                                            @change="handleDetailsUpload"
                                            class="absolute inset-0 cursor-pointer opacity-0"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Content -->
                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8"
                    >
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700"
                                    >Nama Produk</label
                                >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Masukkan nama lengkap produk..."
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 text-base transition-all outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/5"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700"
                                    >Deskripsi</label
                                >
                                <textarea
                                    v-model="form.description"
                                    rows="8"
                                    placeholder="Tuliskan spesifikasi produk, material, dan keunggulan..."
                                    class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition-all outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/5"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: Settings & Sidebar -->
                <div class="w-full space-y-6 lg:w-[380px]">
                    <!-- Card: Pricing & Specs -->
                    <div class="sticky top-6 space-y-6">
                        <div
                            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
                        >
                            <h3
                                class="mb-5 font-['Montserrat'] text-sm font-bold tracking-widest text-slate-400 uppercase"
                            >
                                Pengaturan Produk
                            </h3>

                            <div class="space-y-6">
                                <!-- Price -->
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Harga Jual (IDR)</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4"
                                        >
                                            <span
                                                class="text-sm font-bold text-slate-400"
                                                >Rp</span
                                            >
                                        </div>
                                        <input
                                            v-model="formattedPrice"
                                            type="text"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pr-4 pl-12 font-mono text-lg font-bold text-indigo-600 transition-all outline-none focus:border-indigo-500 focus:bg-white"
                                        />
                                    </div>
                                </div>

                                <!-- Shoe Model -->
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Model Sepatu (Blueprint)</label
                                    >
                                    <select
                                        v-model="form.shoe_model_id"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition-all outline-none focus:border-indigo-500 focus:bg-white"
                                    >
                                        <option :value="null">
                                            Tanpa Model (Hanya Katalog)
                                        </option>
                                        <option
                                            v-for="model in shoeModels"
                                            :key="model.id"
                                            :value="model.id"
                                        >
                                            {{ model.name }}
                                        </option>
                                    </select>
                                    <p class="text-[10px] font-semibold text-slate-400">Hubungkan ke model untuk mengaktifkan fitur Studio Custom.</p>
                                </div>

                                <!-- Collection -->
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Kategori</label
                                    >
                                    <select
                                        v-model="form.collection"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition-all outline-none focus:border-indigo-500 focus:bg-white"
                                        required
                                    >
                                        <option value="" disabled>
                                            Pilih Koleksi
                                        </option>
                                        <option value="Lifestyle">
                                            Lifestyle
                                        </option>
                                        <option value="Running">Running</option>
                                        <option value="Basketball">
                                            Basketball
                                        </option>
                                        <option value="Skateboarding">
                                            Skateboarding
                                        </option>
                                    </select>
                                </div>

                                <!-- Sizes -->
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Range Ukuran (EU)</label
                                    >
                                    <div class="grid grid-cols-2 gap-3">
                                        <input
                                            v-model.number="form.min_size"
                                            type="number"
                                            placeholder="Min"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-center text-sm outline-none focus:border-indigo-500 focus:bg-white"
                                        />
                                        <input
                                            v-model.number="form.max_size"
                                            type="number"
                                            placeholder="Max"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-center text-sm outline-none focus:border-indigo-500 focus:bg-white"
                                        />
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="mt-2 space-y-3">
                                    <label
                                        class="text-source-sans text-sm font-bold text-slate-700"
                                        >Status Stok</label
                                    >
                                    <div class="mt-3 flex flex-col gap-2">
                                        <button
                                            v-for="status in [
                                                'ready',
                                                'po',
                                                'habis',
                                            ]"
                                            :key="status"
                                            type="button"
                                            @click="form.status = status as any"
                                            :class="
                                                form.status === status
                                                    ? 'border-indigo-600 bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600'
                                                    : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                                            "
                                            class="rounded-xl border px-4 py-2.5 text-left text-xs font-bold tracking-tighter uppercase transition-all"
                                        >
                                            <div
                                                class="flex items-center justify-between"
                                            >
                                                <span>{{ status }}</span>
                                                <div
                                                    v-if="
                                                        form.status === status
                                                    "
                                                    class="h-2 w-2 rounded-full bg-indigo-600"
                                                ></div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-3">
                            <button
                                type="submit"
                                class="group relative flex w-full items-center justify-center gap-3 overflow-hidden rounded-2xl bg-slate-900 px-8 py-4 text-sm font-bold text-white transition-all hover:bg-indigo-600 hover:shadow-xl hover:shadow-indigo-200"
                            >
                                <span>Simpan Produk</span>
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 transition-transform group-hover:translate-x-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"
                                    />
                                </svg>
                            </button>
                            <Link
                                :href="katalog.url()"
                                class="w-full rounded-2xl border border-slate-200 bg-white py-4 text-center text-sm font-bold text-slate-500 transition-all hover:bg-slate-50"
                            >
                                Batalkan
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>

<style scoped>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type='number'] {
    -moz-appearance: textfield;
}
</style>
