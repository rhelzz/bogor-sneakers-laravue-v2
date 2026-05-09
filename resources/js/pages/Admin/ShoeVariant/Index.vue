<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { modelSepatu } from '@/routes/admin/';
import shoeTypesRoute from '@/routes/admin/shoe-types/';
import variantsRoute from '@/routes/admin/variants/';

interface SVGAsset {
    id: number;
    file_name: string;
    file_path: string;
}

interface ShoeType {
    id: number;
    shoe_model_id: number;
    name: string;
    slug: string;
}

interface ShoeVariant {
    id: number;
    shoe_model_id: number;
    shoe_type_id: number | null;
    name: string;
    svgs: SVGAsset[];
    type?: ShoeType | null;
}

interface ShoeModel {
    id: number;
    name: string;
    slug: string;
    types: ShoeType[];
}

const props = defineProps<{
    shoeModel: ShoeModel;
    variants: ShoeVariant[];
    types: ShoeType[];
}>();

const showVariantModal = ref(false);
const showUploadModal = ref(false);
const showTypeModal = ref(false);
const showMoveModal = ref(false);
const isFakeLoading = ref(false);
const selectedVariant = ref<ShoeVariant | null>(null);
const editingType = ref<ShoeType | null>(null);

const activeTab = ref<number | 'uncategorized' | 'all'>(
    props.types.length > 0 ? props.types[0].id : 'all'
);

const filteredVariants = computed(() => {
    if (activeTab.value === 'all') return props.variants;
    if (activeTab.value === 'uncategorized') return props.variants.filter(v => !v.shoe_type_id);
    return props.variants.filter(v => v.shoe_type_id === activeTab.value);
});

const hasUncategorized = computed(() => props.variants.some(v => !v.shoe_type_id));

const variantForm = useForm({
    name: '',
    shoe_type_id: null as number | null,
});

const typeForm = useForm({
    name: '',
});

const moveForm = useForm({
    from_type_id: null as number | null,
    to_type_id: null as number | null,
});

const uploadForm = useForm({
    files: [] as File[],
});

const openVariantModal = () => {
    variantForm.reset();
    // Auto select type if we are in a type tab
    if (typeof activeTab.value === 'number') {
        variantForm.shoe_type_id = activeTab.value;
    }
    showVariantModal.value = true;
};

const openUploadModal = (variant: ShoeVariant) => {
    selectedVariant.value = variant;
    uploadForm.reset();
    showUploadModal.value = true;
};

const openTypeModal = (type: ShoeType | null = null) => {
    editingType.value = type;
    if (type) {
        typeForm.name = type.name;
    } else {
        typeForm.reset();
    }
    showTypeModal.value = true;
};

const openMoveModal = () => {
    moveForm.from_type_id = activeTab.value === 'uncategorized' ? null : (activeTab.value as number);
    moveForm.to_type_id = null;
    showMoveModal.value = true;
};

const closeModals = () => {
    showVariantModal.value = false;
    showUploadModal.value = false;
    showTypeModal.value = false;
    showMoveModal.value = false;
    selectedVariant.value = null;
    editingType.value = null;
    isFakeLoading.value = false;
    
    // Reset all forms
    variantForm.reset();
    typeForm.reset();
    moveForm.reset();
    uploadForm.reset();
};

const submitVariant = () => {
    isFakeLoading.value = true;
    
    // Fake loading 1 second
    setTimeout(() => {
        variantForm.post(modelSepatu.variants.store.url(props.shoeModel.id), {
            onSuccess: () => closeModals(),
            onFinish: () => {
                isFakeLoading.value = false;
            }
        });
    }, 1000);
};

const submitType = () => {
    if (editingType.value) {
        typeForm.put(shoeTypesRoute.update.url(editingType.value.id), {
            onSuccess: () => closeModals(),
        });
    } else {
        typeForm.post(modelSepatu.types.store.url(props.shoeModel.id), {
            onSuccess: (page: any) => {
                // Switch to the newly created type tab
                const newTypes = (page.props.types as ShoeType[]);
                if (newTypes.length > 0) {
                    activeTab.value = newTypes[newTypes.length - 1].id;
                }
                closeModals();
            },
        });
    }
};

const submitMove = () => {
    moveForm.post(modelSepatu.variants.moveAll.url(props.shoeModel.id), {
        onSuccess: () => {
            if (moveForm.to_type_id) {
                activeTab.value = moveForm.to_type_id;
            }
            closeModals();
        },
    });
};

const deleteType = (type: ShoeType) => {
    if (confirm(`Peringatan! Menghapus jenis "${type.name}" akan menghapus seluruh varian dan aset SVG di dalamnya secara permanen. Lanjutkan?`)) {
        useForm({}).delete(shoeTypesRoute.destroy.url(type.id), {
            onSuccess: () => {
                activeTab.value = props.types.length > 0 ? props.types[0].id : 'all';
            }
        });
    }
};

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;

    if (target.files) {
        uploadForm.files = Array.from(target.files);
    }
};

const submitUpload = () => {
    if (!selectedVariant.value) {
        return;
    }

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

    if (lower.includes('_base')) {
        return 'Base Layer';
    }

    if (lower.includes('_pola base')) {
        return 'Pattern Base';
    }

    if (lower.includes('_pola aksen')) {
        return 'Pattern Accent';
    }

    if (lower.includes('_aksen')) {
        return 'Accent Layer';
    }

    if (lower.includes('full')) {
        return 'Full Preview';
    }

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
                <div class="flex space-x-3">
                    <button
                        @click="openTypeModal()"
                        class="flex items-center space-x-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 font-bold text-slate-600 shadow-sm transition-all duration-200 hover:bg-slate-50"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Kelola Jenis</span>
                    </button>
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
            </div>

            <!-- Tabs Navigation -->
            <div v-if="types.length > 0" class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-1">
                <button
                    v-for="type in types"
                    :key="type.id"
                    @click="activeTab = type.id"
                    :class="[
                        'relative px-4 py-2 text-sm font-bold transition-all',
                        activeTab === type.id ? 'text-indigo-600' : 'text-slate-500 hover:text-slate-700'
                    ]"
                >
                    {{ type.name }}
                    <div v-if="activeTab === type.id" class="absolute bottom-0 left-0 h-0.5 w-full bg-indigo-600"></div>
                </button>
                <button
                    v-if="hasUncategorized"
                    @click="activeTab = 'uncategorized'"
                    :class="[
                        'relative px-4 py-2 text-sm font-bold transition-all',
                        activeTab === 'uncategorized' ? 'text-amber-600' : 'text-slate-500 hover:text-slate-700'
                    ]"
                >
                    Belum Dikategorikan
                    <div v-if="activeTab === 'uncategorized'" class="absolute bottom-0 left-0 h-0.5 w-full bg-amber-600"></div>
                </button>
                <button
                    @click="activeTab = 'all'"
                    :class="[
                        'relative px-4 py-2 text-sm font-bold transition-all',
                        activeTab === 'all' ? 'text-slate-800' : 'text-slate-500 hover:text-slate-700'
                    ]"
                >
                    Semua
                    <div v-if="activeTab === 'all'" class="absolute bottom-0 left-0 h-0.5 w-full bg-slate-800"></div>
                </button>

                <!-- Move All Action -->
                <div v-if="activeTab !== 'all' && filteredVariants.length > 0" class="ml-auto pb-1">
                    <button 
                        @click="openMoveModal"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-700 underline flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Pindahkan Semua Varian di Tab ini
                    </button>
                </div>
            </div>

            <!-- Variants List -->
            <div v-if="filteredVariants.length > 0" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div v-for="variant in filteredVariants" :key="variant.id" class="flex flex-col rounded-2xl border border-slate-200 bg-white shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center justify-between border-b border-slate-100 p-5">
                        <div class="flex items-center space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-bold">
                                {{ variant.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800">{{ variant.name }}</h3>
                                <p v-if="variant.type" class="text-[10px] font-bold text-indigo-500 uppercase tracking-tight">Jenis: {{ variant.type.name }}</p>
                                <p v-else class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">Tanpa Jenis</p>
                            </div>
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
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showVariantModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 -translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 -translate-y-4"
                    appear
                >
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

                            <div v-if="types.length > 0">
                                <label class="mb-1 block text-sm font-bold text-slate-700">Pilih Jenis (Opsional)</label>
                                <select
                                    v-model="variantForm.shoe_type_id"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                                >
                                    <option :value="null">Tanpa Jenis</option>
                                    <option v-for="type in types" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="closeModals" class="rounded-xl px-5 py-2.5 font-bold text-slate-500 hover:bg-slate-100 transition-colors">Batal</button>
                                <button
                                    type="submit"
                                    :disabled="isFakeLoading || variantForm.processing"
                                    class="rounded-xl bg-indigo-600 px-8 py-2.5 font-bold text-white shadow-sm transition-all hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ (isFakeLoading || variantForm.processing) ? 'Menyimpan...' : 'Simpan Varian' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>

        <!-- Manage Types Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showTypeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 -translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 -translate-y-4"
                    appear
                >
                    <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl text-left">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-xl font-bold text-slate-800">Kelola Jenis Sepatu</h3>
                            <button @click="closeModals" class="text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- List of Types -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Daftar Jenis</h4>
                                <div v-if="types.length > 0" class="space-y-2">
                                    <div v-for="type in types" :key="type.id" class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <span class="font-bold text-slate-700">{{ type.name }}</span>
                                        <div class="flex space-x-1">
                                            <button @click="openTypeModal(type)" class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <button @click="deleteType(type)" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="py-10 text-center text-slate-400 border-2 border-dashed border-slate-100 rounded-xl">
                                    Belum ada jenis
                                </div>
                            </div>

                            <!-- Add/Edit Form -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                                    {{ editingType ? 'Edit Jenis' : 'Tambah Jenis' }}
                                </h4>
                                <form @submit.prevent="submitType" class="space-y-4">
                                    <div>
                                        <label class="mb-1 block text-sm font-bold text-slate-700">Nama Jenis</label>
                                        <input
                                            v-model="typeForm.name"
                                            type="text"
                                            required
                                            placeholder="Misal: Rubber, Phylon, dll"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                                        />
                                    </div>
                                    <div class="flex space-x-2">
                                        <button
                                            v-if="editingType"
                                            type="button"
                                            @click="editingType = null; typeForm.reset()"
                                            class="flex-1 rounded-xl bg-slate-100 px-4 py-2.5 font-bold text-slate-600 hover:bg-slate-200 transition-all"
                                        >
                                            Batal Edit
                                        </button>
                                        <button
                                            type="submit"
                                            :disabled="typeForm.processing"
                                            class="flex-1 rounded-xl bg-indigo-600 px-4 py-2.5 font-bold text-white shadow-sm transition-all hover:bg-indigo-700 disabled:opacity-50"
                                        >
                                            {{ editingType ? 'Update' : 'Tambah' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>

        <!-- Bulk Move Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showMoveModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 -translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 -translate-y-4"
                    appear
                >
                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-left">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-xl font-bold text-slate-800">Pindahkan Semua Varian</h3>
                            <button @click="closeModals" class="text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submitMove" class="space-y-4">
                            <div class="p-4 bg-indigo-50 rounded-xl border border-indigo-100 text-sm text-indigo-700">
                                Anda akan memindahkan semua varian dari tab 
                                <span class="font-bold underline">{{ activeTab === 'uncategorized' ? 'Belum Dikategorikan' : types.find(t => t.id === activeTab)?.name }}</span> 
                                ke jenis yang dipilih di bawah ini.
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-bold text-slate-700">Pindahkan Ke Jenis:</label>
                                <select
                                    v-model="moveForm.to_type_id"
                                    required
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                                >
                                    <option :value="null" disabled>Pilih Jenis Tujuan</option>
                                    <option v-for="type in types.filter(t => t.id !== activeTab)" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <button type="button" @click="closeModals" class="rounded-xl px-5 py-2.5 font-bold text-slate-500 hover:bg-slate-100 transition-colors">Batal</button>
                                <button
                                    type="submit"
                                    :disabled="moveForm.processing || !moveForm.to_type_id"
                                    class="rounded-xl bg-indigo-600 px-8 py-2.5 font-bold text-white shadow-sm transition-all hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ moveForm.processing ? 'Memproses...' : 'Pindahkan Sekarang' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>

        <!-- Bulk Upload Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showUploadModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 -translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 -translate-y-4"
                    appear
                >
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
                </Transition>
            </div>
        </Transition>
    </AdminLayout>
</template>
