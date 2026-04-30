<template>
    <AdminLayout>
        <template #header> Manajemen Pre-order </template>

        <div class="space-y-6 font-hind">
            <!-- Top Action Bar -->
            <div
                class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >
                <div class="flex items-center space-x-3">
                    <h3
                        class="font-montserrat text-lg font-bold tracking-tight text-slate-800"
                    >
                        Daftar Pre-order Aktif
                    </h3>
                    <span
                        class="rounded-lg bg-indigo-100 px-3 py-1 text-sm font-bold text-indigo-700"
                    >
                        Total: {{ assignments.length }} Item
                    </span>
                </div>
                <button
                    @click="openModal('add')"
                    class="flex items-center space-x-2 rounded-xl bg-indigo-600 px-5 py-2.5 font-semibold text-white transition-colors duration-200 hover:bg-indigo-700"
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
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    <span>Tambah Pre-order</span>
                </button>
            </div>

            <!-- Data Table -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50 text-xs tracking-wider text-slate-500 uppercase"
                            >
                                <th class="px-6 py-4 font-bold">Urutan</th>
                                <th class="px-6 py-4 font-bold">
                                    Produk / Katalog
                                </th>
                                <th class="px-6 py-4 font-bold">Batch Label</th>
                                <th class="px-6 py-4 text-center font-bold">
                                    Slot
                                </th>
                                <th class="px-6 py-4 text-center font-bold">
                                    Batas Waktu
                                </th>
                                <th class="px-6 py-4 text-center font-bold">
                                    Status
                                </th>
                                <th class="px-6 py-4 text-center font-bold">
                                    Visibilitas
                                </th>
                                <th class="px-6 py-4 text-right font-bold">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="item in assignments"
                                :key="item.id"
                                class="transition-colors duration-150 hover:bg-slate-50/50"
                            >
                                <!-- Urutan -->
                                <td class="px-6 py-4">
                                    <span
                                        class="font-montserrat font-bold text-slate-800"
                                        >#{{ item.urutan }}</span
                                    >
                                </td>

                                <!-- Produk -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="h-10 w-10 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-200"
                                        >
                                            <img
                                                v-if="item.catalog && item.catalog.image_url && !imageLoadErrors.has(item.id)"
                                                :src="item.catalog.image_url"
                                                alt="Product"
                                                class="h-full w-full object-cover"
                                                @error="trackImageError(item.id)"
                                            />
                                            <!-- Fallback UI -->
                                            <div v-else class="flex h-full w-full items-center justify-center bg-slate-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div v-if="item.catalog">
                                            <p
                                                class="text-sm font-bold text-slate-800"
                                            >
                                                {{ item.catalog.name }}
                                            </p>
                                            <p
                                                class="text-xs font-semibold text-slate-500"
                                            >
                                                {{ item.catalog.code }}
                                            </p>
                                        </div>
                                        <div v-else>
                                            <p class="text-sm italic text-slate-500">Katalog dihapus</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Batch -->
                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-md border border-slate-200 bg-slate-100 px-2.5 py-1 text-sm font-semibold text-slate-700"
                                    >
                                        {{ item.batch_label || '-' }}
                                    </span>
                                </td>

                                <!-- Slot -->
                                <td class="px-6 py-4 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >
                                        <span
                                            class="text-sm font-bold text-slate-800"
                                            >{{ item.filled_slots }} /
                                            {{ item.max_slots }}</span
                                        >
                                        <div
                                            class="max-w-60px mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200"
                                        >
                                            <div
                                                class="h-full rounded-full transition-all duration-500"
                                                :class="
                                                    getSlotColor(
                                                        item.filled_slots,
                                                        item.max_slots,
                                                    )
                                                "
                                                :style="{
                                                    width: `${(item.filled_slots / item.max_slots) * 100}%`,
                                                }"
                                            ></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Countdown -->
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-sm font-semibold text-slate-600"
                                        >{{
                                            formatDate(item.countdown_ends_at)
                                        }}</span
                                    >
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="rounded-md px-3 py-1 text-xs font-bold"
                                        :class="getStatusClass(item.status)"
                                    >
                                        {{ formatStatus(item.status) }}
                                    </span>
                                </td>

                                <!-- Visibilitas -->
                                <td class="px-6 py-4 text-center">
                                    <button
                                        type="button"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :style="{
                                            backgroundColor: item.is_visible
                                                ? '#10b981'
                                                : '#cbd5e1',
                                        }"
                                        @click="toggleVisibility(item)"
                                        role="switch"
                                        :aria-checked="item.is_visible"
                                    >
                                        <span
                                            class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                                            :style="{
                                                transform: item.is_visible
                                                    ? 'translateX(1.25rem)'
                                                    : 'translateX(0)',
                                            }"
                                        />
                                    </button>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div
                                        class="flex items-center justify-end space-x-2"
                                    >
                                        <button
                                            @click="openModal('edit', item)"
                                            class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                                            title="Edit"
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
                                                    stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                                />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteAssignment(item)"
                                            class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                                            title="Hapus"
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
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="assignments.length === 0">
                                <td
                                    colspan="8"
                                    class="px-6 py-12 text-center font-semibold text-slate-500"
                                >
                                    Belum ada data pre-order.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Form (Add / Edit) -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isModalOpen"
                class="font-hind fixed inset-0 z-50 flex items-center justify-center p-4"
            >
                <!-- Backdrop -->
                <div
                    class="absolute inset-0 bg-[#15161a]/60 backdrop-blur-sm"
                    @click="closeModal"
                ></div>

                <!-- Modal Content -->
                <Transition
                    enter-active-class="transition duration-300 ease-out delay-75"
                    enter-from-class="opacity-0 translate-y-8 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-8 scale-95"
                >
                    <div
                        v-if="isModalOpen"
                        class="relative flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                    >
                        <!-- Modal Header -->
                        <div
                            class="flex shrink-0 items-center justify-between border-b border-slate-100 bg-slate-50 px-6 py-4"
                        >
                            <h3
                                class="font-montserrat text-xl font-bold text-slate-800"
                            >
                                {{
                                    modalMode === 'add'
                                        ? 'Tambah Pre-order'
                                        : 'Edit Pre-order'
                                }}
                            </h3>
                            <button
                                @click="closeModal"
                                class="rounded-xl p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-500 focus:outline-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
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

                        <!-- Modal Body (Scrollable) -->
                        <div class="flex-1 overflow-y-auto p-6 space-y-5">
                            <!-- Global Form Error -->
                            <div v-if="Object.keys(formError).length > 0" class="rounded-lg bg-rose-50 p-4 text-sm text-rose-600">
                                <ul class="list-disc pl-5">
                                    <li v-for="(errors, field) in formError" :key="field">
                                        {{ errors[0] }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Form Grid -->
                            <form @submit.prevent="saveAssignment" id="preOrderForm" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <!-- Catalog ID / Produk -->
                                <div class="col-span-1 md:col-span-2">
                                    <label
                                        class="mb-1.5 block text-sm font-bold text-slate-700"
                                        >Katalog Produk <span class="text-rose-500">*</span></label
                                    >
                                    <select
                                        v-model="formPayload.catalog_id"
                                        required
                                        class="w-full cursor-pointer appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                        :class="{'border-rose-500': formError.catalog_id}"
                                    >
                                        <option value="" disabled>
                                            Pilih Produk...
                                        </option>
                                        <option
                                            v-for="opt in catalogOptions"
                                            :key="opt.id"
                                            :value="opt.id"
                                            :disabled="opt.is_assigned && opt.assignment_id !== selectedItem?.id"
                                        >
                                            {{ opt.name }} ({{ opt.code }}) 
                                            <span v-if="opt.is_assigned && opt.assignment_id !== selectedItem?.id"> - Sudah Dipilih</span>
                                        </option>
                                    </select>
                                </div>

                                <!-- Batch Label -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-bold text-slate-700"
                                        >Batch Label</label
                                    >
                                    <input
                                        v-model="formPayload.batch_label"
                                        type="text"
                                        placeholder="Contoh: Batch 1"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                        :class="{'border-rose-500': formError.batch_label}"
                                    />
                                </div>

                                <!-- Urutan (Sort Order) -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-bold text-slate-700"
                                        >Urutan Tampil</label
                                    >
                                    <input
                                        v-model="formPayload.urutan"
                                        type="number"
                                        placeholder="Otomatis"
                                        min="1"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                        :class="{'border-rose-500': formError.urutan}"
                                    />
                                </div>

                                <!-- Status -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-bold text-slate-700"
                                        >Status <span class="text-rose-500">*</span></label
                                    >
                                    <select
                                        v-model="formPayload.status"
                                        required
                                        class="w-full cursor-pointer appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                        :class="{'border-rose-500': formError.status}"
                                    >
                                        <option value="buka">
                                            Buka (Open)
                                        </option>
                                        <option value="hampir_habis">
                                            Hampir Habis
                                        </option>
                                        <option value="habis">
                                            Habis (Closed)
                                        </option>
                                    </select>
                                </div>

                                <!-- Batas Waktu -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-bold text-slate-700"
                                        >Batas Waktu <span class="text-rose-500">*</span></label
                                    >
                                    <input
                                        v-model="formPayload.countdown_ends_at"
                                        type="datetime-local"
                                        required
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                        :class="{'border-rose-500': formError.countdown_ends_at}"
                                    />
                                </div>

                                <!-- Maksimal Slot -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-bold text-slate-700"
                                        >Maksimal Slot <span class="text-rose-500">*</span></label
                                    >
                                    <input
                                        v-model="formPayload.max_slots"
                                        type="number"
                                        min="1"
                                        required
                                        placeholder="Misal: 50"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                        :class="{'border-rose-500': formError.max_slots}"
                                    />
                                </div>

                                <!-- Slot Terisi -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-sm font-bold text-slate-700"
                                        >Slot Terisi <span class="text-rose-500">*</span></label
                                    >
                                    <input
                                        v-model="formPayload.filled_slots"
                                        type="number"
                                        min="0"
                                        required
                                        placeholder="Misal: 10"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                                        :class="{'border-rose-500': formError.filled_slots}"
                                    />
                                </div>

                                <!-- Switch Visibilitas -->
                                <div
                                    class="col-span-1 mt-2 flex items-center space-x-3 rounded-xl border border-slate-100 bg-slate-50 p-4 md:col-span-2"
                                >
                                    <button
                                        type="button"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :style="{
                                            backgroundColor:
                                                formPayload.is_visible
                                                    ? '#10b981'
                                                    : '#cbd5e1',
                                        }"
                                        @click="
                                            formPayload.is_visible =
                                                !formPayload.is_visible
                                        "
                                        role="switch"
                                        :aria-checked="formPayload.is_visible"
                                    >
                                        <span
                                            class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                                            :style="{
                                                transform:
                                                    formPayload.is_visible
                                                        ? 'translateX(1.25rem)'
                                                        : 'translateX(0)',
                                            }"
                                        />
                                    </button>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-slate-800"
                                            >Tampilkan di Halaman Utama</span
                                        >
                                        <span
                                            class="text-xs font-semibold text-slate-500"
                                            >Jika aktif, item ini akan muncul di
                                            beranda pelanggan.</span
                                        >
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Modal Footer -->
                        <div
                            class="flex shrink-0 items-center justify-end space-x-3 border-t border-slate-100 bg-slate-50 px-6 py-4"
                        >
                            <button
                                type="button"
                                @click="closeModal"
                                class="rounded-xl px-5 py-2.5 font-bold text-slate-600 transition-colors hover:bg-slate-200 hover:text-slate-800 focus:outline-none"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                form="preOrderForm"
                                :disabled="isLoading"
                                class="rounded-xl bg-indigo-600 px-6 py-2.5 font-bold text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none disabled:bg-indigo-400"
                            >
                                {{ isLoading ? 'Menyimpan...' : 'Simpan Data' }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </AdminLayout>
</template>

<script setup lang="ts">
import { usePreOrderHome } from '@/composables/Admin/usePreOrderHome';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { AdminAssignment, CatalogOption } from '@/types/Admin/preOrderHome';

interface Props {
    assignments: AdminAssignment[];
    catalogOptions: CatalogOption[];
    visibleCount: number;
    visibleLimit: number;
}

const props = defineProps<Props>();

const {
    assignments,
    catalogOptions,
    isModalOpen,
    modalMode,
    selectedItem,
    formPayload,
    isLoading,
    formError,
    imageLoadErrors,
    openModal,
    closeModal,
    saveAssignment,
    deleteAssignment,
    toggleVisibility,
    trackImageError,
    getStatusClass,
    formatStatus,
    getSlotColor,
    formatDate,
} = usePreOrderHome(props.assignments, props.catalogOptions, props.visibleLimit);

</script>
