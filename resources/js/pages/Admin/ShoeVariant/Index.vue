<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { modelSepatu, variants as variantsRoute } from '@/routes/admin';

interface SVGAsset {
    id: number;
    file_name: string;
    file_path: string;
}

interface ShoeVariant {
    id: number;
    name: string;
    svgs: SVGAsset[];
}

interface ShoeModel {
    id: number;
    name: string;
    slug: string;
}

const props = defineProps<{
    shoeModel: ShoeModel;
    variants: ShoeVariant[];
}>();

const showVariantModal = ref(false);
const showUploadModal = ref(false);
const selectedVariant = ref<ShoeVariant | null>(null);

const variantForm = useForm({
    name: '',
});

const uploadForm = useForm({
    files: [] as File[],
});

const openVariantModal = () => {
    variantForm.reset();
    showVariantModal.value = true;
};

const openUploadModal = (variant: ShoeVariant) => {
    selectedVariant.value = variant;
    uploadForm.reset();
    showUploadModal.value = true;
};

const closeModals = () => {
    showVariantModal.value = false;
    showUploadModal.value = false;
    selectedVariant.value = null;
};

const submitVariant = () => {
    variantForm.post(modelSepatu.variants.store.url(props.shoeModel.id), {
        onSuccess: () => closeModals(),
    });
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        uploadForm.files = Array.from(target.files);
    }
};

const submitUpload = () => {
    if (!selectedVariant.value) return;
    
    uploadForm.post(variantsRoute.svgs.upload.url(selectedVariant.value.id), {
        onSuccess: () => closeModals(),
    });
};

const deleteVariant = (variant: ShoeVariant) => {
    if (confirm(`Apakah Anda yakin ingin menghapus varian "${variant.name}" beserta semua aset SVG-nya?`)) {
        useForm({}).delete(variantsRoute.destroy.url(variant.id));
    }
};

// Function to helper determine if a file is an accent, base or pattern
const getLayerTypeLabel = (fileName: string) => {
    const lower = fileName.toLowerCase();
    if (lower.includes('_base')) return 'Base Layer';
    if (lower.includes('_pola base')) return 'Pattern Base';
    if (lower.includes('_pola aksen')) return 'Pattern Accent';
    if (lower.includes('_aksen')) return 'Accent Layer';
    if (lower.includes('full')) return 'Full Preview';
    return 'Other';
};
</script>

<template>
    <Head :title="`Varian: ${shoeModel.name}`" />

    <AdminLayout>
        <template #header>
            <div class="flex items-center space-x-2 text-sm md:text-base">
                <Link :href="modelSepatu.url()" class="text-slate-400 hover:text-indigo-600 transition-colors">Kelola Model</Link>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-800">{{ shoeModel.name }}</span>
            </div>
        </template>

        <div class="space-y-6 font-['Source_Sans_Pro']">
            <!-- Header Action -->
            <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Daftar Varian</h2>
                    <p class="text-sm text-slate-500">Kelola kombinasi warna dan aset SVG untuk model ini.</p>
                </div>
                <button
                    @click="openVariantModal"
                    class="flex items-center space-x-2 rounded-xl bg-indigo-600 px-5 py-2.5 font-bold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Varian</span>
                </button>
            </div>

            <!-- Variants List -->
            <div v-if="variants.length > 0" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div v-for="variant in variants" :key="variant.id" class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between border-b border-slate-100 p-5">
                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-bold">
                                {{ variant.name.charAt(0).toUpperCase() }}
                            </div>
                            <h3 class="font-bold text-slate-800">{{ variant.name }}</h3>
                        </div>
                        <div class="flex space-x-2">
                            <button 
                                @click="openUploadModal(variant)"
                                class="rounded-lg border border-slate-200 p-2 text-slate-400 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
                                title="Bulk Upload SVG"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </button>
                            <button 
                                @click="deleteVariant(variant)"
                                class="rounded-lg border border-slate-200 p-2 text-slate-400 transition-all hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                title="Hapus Varian"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 p-5">
                        <div v-if="variant.svgs.length > 0" class="space-y-3">
                            <div class="flex items-center justify-between text-xs font-bold tracking-widest text-slate-400 uppercase">
                                <span>Aset SVG ({{ variant.svgs.length }})</span>
                            </div>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <div v-for="svg in variant.svgs" :key="svg.id" class="flex items-center space-x-3 rounded-xl border border-slate-100 bg-slate-50 p-2 transition-colors hover:bg-white">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white shadow-sm border border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-xs font-bold text-slate-700">{{ svg.file_name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">{{ getLayerTypeLabel(svg.file_name) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex h-32 flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-100 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mb-2 h-8 w-8 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm">Belum ada aset SVG</p>
                            <button @click="openUploadModal(variant)" class="mt-2 text-xs font-bold text-indigo-600 hover:underline">Upload Bulk</button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white py-20 text-slate-400 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="mb-4 h-16 w-16 opacity-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-lg font-bold">Belum Ada Varian</p>
                <p class="mb-6 text-sm">Tambahkan varian warna untuk memulai mengupload aset kustomisasi.</p>
                <button
                    @click="openVariantModal"
                    class="rounded-xl bg-indigo-600 px-8 py-2.5 font-bold text-white shadow-lg transition-all hover:bg-indigo-700 hover:shadow-indigo-500/20"
                >
                    Tambah Varian Pertama
                </button>
            </div>
        </div>

        <!-- Add Variant Modal -->
        <div v-if="showVariantModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-800">Tambah Varian Baru</h3>
                    <button @click="closeModals" class="text-slate-400 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitVariant" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-bold text-slate-700">Nama Varian</label>
                        <input 
                            v-model="variantForm.name"
                            type="text" 
                            required
                            placeholder="Contoh: Varian 1 / Black Gum"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                        />
                        <p v-if="variantForm.errors.name" class="mt-1 text-xs text-rose-500">{{ variantForm.errors.name }}</p>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="closeModals" class="rounded-xl px-5 py-2.5 font-bold text-slate-500 hover:bg-slate-100 transition-colors">Batal</button>
                        <button 
                            type="submit"
                            :disabled="variantForm.processing"
                            class="rounded-xl bg-indigo-600 px-8 py-2.5 font-bold text-white shadow-sm transition-all hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ variantForm.processing ? 'Menyimpan...' : 'Simpan Varian' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Upload Modal -->
        <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Bulk Upload SVG</h3>
                        <p class="text-sm text-slate-500">Varian: {{ selectedVariant?.name }}</p>
                    </div>
                    <button @click="closeModals" class="text-slate-400 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitUpload" class="space-y-6">
                    <div 
                        class="relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-10 transition-colors hover:border-indigo-300 hover:bg-indigo-50/30 group"
                    >
                        <input 
                            type="file" 
                            multiple
                            accept=".svg"
                            class="absolute inset-0 cursor-pointer opacity-0"
                            @change="handleFileChange"
                        />
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm transition-transform group-hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <p class="mt-4 font-bold text-slate-700">Klik atau drop file SVG di sini</p>
                        <p class="text-xs text-slate-400">Dukungan format .svg (Multiple files)</p>
                    </div>

                    <div v-if="uploadForm.files.length > 0" class="max-h-40 overflow-y-auto rounded-xl border border-slate-100 bg-slate-50 p-3 space-y-2">
                        <div v-for="(file, idx) in uploadForm.files" :key="idx" class="flex items-center justify-between rounded-lg bg-white p-2 shadow-sm text-xs">
                            <span class="truncate pr-4 font-medium text-slate-600">{{ file.name }}</span>
                            <span class="shrink-0 text-[10px] text-slate-400">{{ (file.size / 1024).toFixed(1) }} KB</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeModals" class="rounded-xl px-5 py-2.5 font-bold text-slate-500 hover:bg-slate-100 transition-colors">Batal</button>
                        <button 
                            type="submit"
                            :disabled="uploadForm.processing || uploadForm.files.length === 0"
                            class="rounded-xl bg-indigo-600 px-8 py-2.5 font-bold text-white shadow-sm transition-all hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{ uploadForm.processing ? 'Mengupload...' : 'Mulai Upload' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
