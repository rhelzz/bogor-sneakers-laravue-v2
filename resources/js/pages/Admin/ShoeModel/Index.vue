<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { modelSepatu } from '@/routes/admin';

interface ShoeModel {
    id: number;
    name: string;
    slug: string;
    is_active: boolean;
    variants_count: number;
    created_at: string;
}

const props = defineProps<{
    models: ShoeModel[];
}>();

const searchQuery = ref('');
const showModal = ref(false);
const editingModel = ref<ShoeModel | null>(null);

const filteredModels = computed(() => {
    if (!searchQuery.value) {
        return props.models;
    }

    const query = searchQuery.value.toLowerCase();
    
    return props.models.filter(model =>
        model.name.toLowerCase().includes(query)
    );
});

const form = useForm({
    name: '',
    is_active: true,
});

const openCreateModal = () => {
    editingModel.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (model: ShoeModel) => {
    editingModel.value = model;
    form.name = model.name;
    form.is_active = model.is_active;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingModel.value) {
        form.put(modelSepatu.update.url(editingModel.value.id), {
            onSuccess: () => closeModal(),
            onError: (errors) => {
                console.error("Update failed:", errors);
            }
        });
    } else {
        form.post(modelSepatu.store.url(), {
            onSuccess: () => closeModal(),
            onError: (errors) => {
                console.error("Creation failed:", errors);
            }
        });
    }
};

const deleteModel = (model: ShoeModel) => {
    if (confirm(`Apakah Anda yakin ingin menghapus model "${model.name}"? Semua varian dan SVG terkait juga akan dihapus.`)) {
        form.delete(modelSepatu.destroy.url(model.id));
    }
};
</script>

<template>
    <Head title="Kelola Model Sepatu" />

    <AdminLayout>
        <template #header> Kelola Model Sepatu </template>

        <div class="space-y-6 font-['Source_Sans_Pro']">
            <!-- Top Action Bar -->
            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-center md:justify-between">
                <div class="flex flex-1 items-center space-x-4">
                    <div class="relative w-full max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama model..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm outline-none transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10"
                        />
                    </div>
                    <span class="hidden rounded-lg bg-indigo-50 px-3 py-1 text-sm font-bold text-indigo-700 md:block">
                        Total: {{ models.length }} Model
                    </span>
                </div>

                <button
                    @click="openCreateModal"
                    class="flex items-center justify-center space-x-2 rounded-xl bg-indigo-600 px-5 py-2.5 font-bold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Model</span>
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-xs font-bold tracking-wider text-slate-500 uppercase">
                                <th class="px-6 py-4">Nama Model</th>
                                <th class="px-6 py-4">Slug</th>
                                <th class="px-6 py-4 text-center">Jumlah Varian</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="model in filteredModels" :key="model.id" class="group transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ model.name }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-sm">
                                    {{ model.slug }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        {{ model.variants_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase shadow-sm"
                                        :class="model.is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'"
                                    >
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full" :class="model.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                        {{ model.is_active ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end space-x-2">
                                        <Link
                                            :href="modelSepatu.variants.url(model.id)"
                                            class="rounded-lg border border-slate-200 p-2 text-slate-400 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600"
                                            title="Kelola Varian"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </Link>
                                        <button
                                            @click="openEditModal(model)"
                                            class="rounded-lg border border-slate-200 p-2 text-slate-400 transition-all hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600"
                                            title="Edit"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteModel(model)"
                                            class="rounded-lg border border-slate-200 p-2 text-slate-400 transition-all hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
                                            title="Hapus"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredModels.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                    Data model sepatu tidak ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 -translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 -translate-y-4"
                    appear
                >
                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-xl font-bold text-slate-800">
                                {{ editingModel ? 'Edit Model Sepatu' : 'Tambah Model Sepatu' }}
                            </h3>
                            <button @click="closeModal" class="text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-bold text-slate-700">Nama Model</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    placeholder="Contoh: Casual High"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                                    :class="{'border-rose-500': form.errors.name}"
                                />
                                <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
                            </div>

                            <div class="flex items-center">
                                <input
                                    v-model="form.is_active"
                                    id="is_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <label for="is_active" class="ml-2 text-sm font-medium text-slate-700">Aktifkan model ini</label>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="rounded-xl px-5 py-2.5 font-bold text-slate-500 hover:bg-slate-100 transition-colors"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-xl bg-indigo-600 px-8 py-2.5 font-bold text-white shadow-sm transition-all hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>
    </AdminLayout>
</template>
