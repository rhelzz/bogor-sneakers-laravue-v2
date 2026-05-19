<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { katalog } from '@/routes/admin';

interface ShoeModel {
    id: number;
    name: string;
}

const props = defineProps<{
    shoeModels: ShoeModel[];
    collections: string[];
    catalog?: any;
}>();

const isSubmitting = ref(false);
const isEditing = computed(() => !!props.catalog);

const form = useForm({
    shoe_model_id: props.catalog?.shoe_model_id ?? null,
    name: props.catalog?.name ?? '',
    collection: props.catalog?.collection ?? '',
    price: props.catalog?.price ?? 0,
    status: props.catalog?.status ?? 'ready' as 'ready' | 'po' | 'habis',
    description: props.catalog?.description ?? '',
    min_size: props.catalog?.sizes?.length ? Math.min(...props.catalog.sizes.map((s: any) => s.size)) : '' as number | string,
    max_size: props.catalog?.sizes?.length ? Math.max(...props.catalog.sizes.map((s: any) => s.size)) : '' as number | string,
    is_active: props.catalog?.is_active ?? true,
    thumbnail: null as File | null,
    images: [] as File[],
});

// Watch min_size to handle disabled state logic
watch(() => form.min_size, (newMin) => {
    const minVal = typeof newMin === 'number' ? newMin : parseInt(newMin as string);

    if (isNaN(minVal)) {
        form.max_size = '';

        return;
    }
});

const handleMaxSizeBlur = () => {
    const minVal = typeof form.min_size === 'number' ? form.min_size : parseInt(form.min_size as string);
    const maxVal = typeof form.max_size === 'number' ? form.max_size : parseInt(form.max_size as string);

    if (isNaN(minVal) || isNaN(maxVal)) {
        return;
    }

    if (maxVal <= minVal) {
        form.max_size = minVal + 1;
    }
};

const isDropdownOpen = ref(false);
const selectedModel = computed(() =>
    props.shoeModels.find(m => m.id === form.shoe_model_id)
);

const selectModel = (id: number) => {
    form.shoe_model_id = id;
    isDropdownOpen.value = false;
};

// Collection Dropdown Logic
const isCollectionDropdownOpen = ref(false);
const isAddingNewCollection = ref(false);
const newCollectionName = ref('');
const newCollectionInput = ref<HTMLInputElement | null>(null);
const localCollections = ref([...props.collections]);

const selectCollection = (collection: string) => {
    form.collection = collection;
    isCollectionDropdownOpen.value = false;
    isAddingNewCollection.value = false;
};

const startAddingCollection = () => {
    isAddingNewCollection.value = true;
    nextTick(() => {
        newCollectionInput.value?.focus();
    });
};

const addNewCollection = () => {
    const name = newCollectionName.value.trim();

    if (name) {
        if (!localCollections.value.includes(name)) {
            localCollections.value.push(name);
            localCollections.value.sort();
        }

        form.collection = name;
        newCollectionName.value = '';
        isAddingNewCollection.value = false;
        isCollectionDropdownOpen.value = false;
    }
};

const handleCollectionBlur = () => {
    if (!newCollectionName.value.trim()) {
        isAddingNewCollection.value = false;
    }
};

const formattedPrice = computed({
    get: () => new Intl.NumberFormat('id-ID').format(form.price),
    set: (val: string) => {
        const numericValue = parseInt(val.replace(/\D/g, '')) || 0;
        form.price = numericValue;
    },
});

const thumbnailPreview = ref<string | null>(props.catalog?.card_image_url ?? null);
const existingImages = ref<any[]>(props.catalog?.images ?? []);
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
        // Limit to 6 total (existing + new)
        const totalExisting = existingImages.value.length;
        const remainingSlots = 6 - (totalExisting + form.images.length);
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

const deleteExistingImage = (imageId: number) => {
    if (confirm('Hapus gambar ini dari galeri?')) {
        router.delete(katalog.images.destroy.url(props.catalog.id, imageId), {
            onSuccess: () => {
                existingImages.value = existingImages.value.filter(img => img.id !== imageId);
            }
        });
    }
};

const submit = () => {
    if (isSubmitting.value) {
        return;
    }

    const min = typeof form.min_size === 'number' ? form.min_size : parseInt(form.min_size as string);
    const max = typeof form.max_size === 'number' ? form.max_size : parseInt(form.max_size as string);

    const sizes: number[] = [];

    if (!isNaN(min) && !isNaN(max) && min <= max) {
        for (let s = min; s <= max; s++) {
            sizes.push(s);
        }
    }

    isSubmitting.value = true;

    const url = isEditing.value 
        ? katalog.update.url(props.catalog.id)
        : katalog.store.url();

    const requestOptions = {
        forceFormData: true,
        onSuccess: () => {
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
            alert(`Gagal ${isEditing.value ? 'memperbarui' : 'menambahkan'} produk. Silakan periksa kembali form Anda.`);
        },
        onFinish: () => {
            if (form.wasSuccessful) return;
            isSubmitting.value = false;
        }
    };

    if (isEditing.value) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
            sizes: sizes,
        })).post(url, requestOptions);
    } else {
        form.transform((data) => ({
            ...data,
            sizes: sizes,
        })).post(url, requestOptions);
    }
};
</script>

<template>
    <Head :title="isEditing ? 'Edit Produk' : 'Tambah Produk Baru'" />

    <AdminLayout>
        <template #header> {{ isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }} </template>

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
                                        existingImages.length + detailPreviews.length
                                    }}/6)</label
                                >
                                <div
                                    class="grid grid-cols-3 gap-3 md:grid-cols-4"
                                >
                                    <!-- Existing Images -->
                                    <div
                                        v-for="img in existingImages"
                                        :key="img.id"
                                        class="group relative aspect-square overflow-hidden rounded-xl border border-slate-200 shadow-sm"
                                    >
                                        <img
                                            :src="img.image_url"
                                            class="h-full w-full object-cover"
                                        />
                                        <button
                                            @click="deleteExistingImage(img.id)"
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

                                    <!-- New Uploads -->
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
                                        v-if="existingImages.length + detailPreviews.length < 6"
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
                <div class="w-full space-y-6 lg:w-95">
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

                                <!-- Collection Dropdown -->
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Koleksi</label
                                    >
                                    <div class="relative">
                                        <button
                                            type="button"
                                            @click="isCollectionDropdownOpen = !isCollectionDropdownOpen"
                                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition-all outline-none focus:border-indigo-500 focus:bg-white"
                                            :class="{ 'ring-2 ring-indigo-500/10 border-indigo-500': isCollectionDropdownOpen }"
                                        >
                                            <span :class="form.collection ? 'text-slate-900' : 'text-slate-400'">
                                                {{ form.collection || 'Pilih Koleksi' }}
                                            </span>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-slate-400 transition-transform duration-300"
                                                :class="{ 'rotate-180': isCollectionDropdownOpen }"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <transition
                                            enter-active-class="transition duration-200 ease-in-out"
                                            enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                                            enter-to-class="transform scale-100 opacity-100 translate-y-0"
                                            leave-active-class="transition duration-150 ease-in-out"
                                            leave-from-class="transform scale-100 opacity-100 translate-y-0"
                                            leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                                        >
                                            <div
                                                v-if="isCollectionDropdownOpen"
                                                class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                                            >
                                                <div class="max-h-60 overflow-y-auto py-1">
                                                    <button
                                                        v-for="item in localCollections"
                                                        :key="item"
                                                        type="button"
                                                        @click="selectCollection(item)"
                                                        class="flex w-full items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                                        :class="{ 'bg-indigo-50 text-indigo-600 font-bold': form.collection === item }"
                                                    >
                                                        {{ item }}
                                                    </button>
                                                    <div v-if="localCollections.length === 0 && !isAddingNewCollection" class="px-4 py-3 text-xs text-slate-400 text-center italic">
                                                        Belum ada koleksi
                                                    </div>
                                                </div>

                                                <!-- Action Bottom -->
                                                <div class="border-t border-slate-100 p-2">
                                                    <transition
                                                        enter-active-class="transition duration-300 ease-out"
                                                        enter-from-class="transform opacity-0 translate-y-2"
                                                        enter-to-class="transform opacity-100 translate-y-0"
                                                        leave-active-class="transition duration-200 ease-in"
                                                        leave-from-class="transform opacity-100 translate-y-0"
                                                        leave-to-class="transform opacity-0 translate-y-2"
                                                        mode="out-in"
                                                    >
                                                        <div v-if="isAddingNewCollection" :key="'input'" class="flex items-center gap-2 p-1">
                                                            <input
                                                                ref="newCollectionInput"
                                                                v-model="newCollectionName"
                                                                type="text"
                                                                placeholder="Nama koleksi..."
                                                                class="flex-1 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:bg-white"
                                                                @keyup.enter="addNewCollection"
                                                                @blur="handleCollectionBlur"
                                                            />
                                                            <button
                                                                type="button"
                                                                @click="addNewCollection"
                                                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white transition-colors hover:bg-indigo-700"
                                                            >
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <button
                                                            v-else
                                                            :key="'button'"
                                                            type="button"
                                                            @click="startAddingCollection"
                                                            class="flex w-full items-center justify-center gap-2 rounded-lg py-2.5 text-xs font-bold text-indigo-600 transition-colors hover:bg-indigo-50"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                            <span>Tambah Koleksi</span>
                                                        </button>
                                                    </transition>
                                                </div>
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Overlay to close dropdown -->
                                    <div v-if="isCollectionDropdownOpen" @click="isCollectionDropdownOpen = false; isAddingNewCollection = false" class="fixed inset-0 z-40 cursor-default"></div>
                                </div>

                                <!-- Shoe Model Dropdown -->
                                <div class="space-y-2">
                                    <label
                                        class="text-sm font-bold text-slate-700"
                                        >Model Sepatu</label
                                    >
                                    <div class="relative">
                                        <button
                                            type="button"
                                            @click="isDropdownOpen = !isDropdownOpen"
                                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition-all outline-none focus:border-indigo-500 focus:bg-white"
                                            :class="{ 'ring-2 ring-indigo-500/10 border-indigo-500': isDropdownOpen }"
                                        >
                                            <span :class="selectedModel ? 'text-slate-900' : 'text-slate-400'">
                                                {{ selectedModel ? selectedModel.name : 'Pilih Model Sepatu' }}
                                            </span>
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-slate-400 transition-transform duration-300"
                                                :class="{ 'rotate-180': isDropdownOpen }"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <transition
                                            enter-active-class="transition duration-200 ease-in-out"
                                            enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                                            enter-to-class="transform scale-100 opacity-100 translate-y-0"
                                            leave-active-class="transition duration-150 ease-in-out"
                                            leave-from-class="transform scale-100 opacity-100 translate-y-0"
                                            leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                                        >
                                            <div
                                                v-if="isDropdownOpen"
                                                class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                                            >
                                                <div class="max-h-60 overflow-y-auto py-1">
                                                    <button
                                                        v-for="model in shoeModels"
                                                        :key="model.id"
                                                        type="button"
                                                        @click="selectModel(model.id)"
                                                        class="flex w-full items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                                        :class="{ 'bg-indigo-50 text-indigo-600 font-bold': form.shoe_model_id === model.id }"
                                                    >
                                                        {{ model.name }}
                                                    </button>
                                                    <div v-if="shoeModels.length === 0" class="px-4 py-3 text-xs text-slate-400 text-center italic">
                                                        Belum ada model sepatu aktif
                                                    </div>
                                                </div>
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Overlay to close dropdown -->
                                    <div v-if="isDropdownOpen" @click="isDropdownOpen = false" class="fixed inset-0 z-40 cursor-default"></div>
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
                                            :disabled="!form.min_size"
                                            :min="(typeof form.min_size === 'number' ? form.min_size : parseInt(form.min_size as string)) + 1"
                                            @blur="handleMaxSizeBlur"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-center text-sm outline-none focus:border-indigo-500 focus:bg-white disabled:cursor-not-allowed disabled:opacity-50"
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
                                :disabled="isSubmitting"
                                class="group relative flex w-full items-center justify-center gap-3 overflow-hidden rounded-2xl bg-slate-900 px-8 py-4 text-sm font-bold text-white transition-all enabled:hover:bg-indigo-600 enabled:hover:shadow-xl enabled:hover:shadow-indigo-200 disabled:opacity-70 disabled:cursor-not-allowed"
                            >
                                <template v-if="isSubmitting">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Menyimpan...</span>
                                </template>
                                <template v-else>
                                    <span>{{ isEditing ? 'Update Produk' : 'Simpan Produk' }}</span>
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
                                </template>
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
