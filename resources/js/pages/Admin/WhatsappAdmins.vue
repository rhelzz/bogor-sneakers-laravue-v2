<script setup lang="ts">
import { defineProps } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useWhatsappAdmin } from '@/composables/Admin/useWhatsappAdmin';
import { WHATSAPP_COLORS } from '@/constants/Admin/whatsappConstants';

const props = defineProps<{
    admins: any[];
    colorOptions: any[];
}>();

const {
    viewMode,
    isModalOpen,
    modalMode,
    form,
    openModal,
    closeModal,
    submitForm,
    deleteAdmin,
    toggleStatus,
    isProcessing,
} = useWhatsappAdmin();
</script>

<template>
    <AdminLayout>
        <template #header> WhatsApp Admin </template>

        <div class="space-y-6 font-['Source_Sans_Pro']">
            <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center space-x-3">
                    <h3 class="font-['Montserrat'] text-lg font-bold tracking-tight text-slate-800">
                        Daftar Kontak Admin
                    </h3>
                    <span class="rounded-lg bg-indigo-100 px-3 py-1 text-sm font-bold text-indigo-700">
                        Total: {{ admins.length }} Kontak
                    </span>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex rounded-xl border border-slate-200 bg-slate-100 p-1">
                        <button
                            @click="viewMode = 'table'"
                            :class="
                                viewMode === 'table'
                                    ? 'bg-white text-indigo-600 shadow-sm'
                                    : 'text-slate-500 hover:text-slate-700'
                            "
                            class="rounded-lg p-2 transition-all duration-200"
                            title="Mode Tabel"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <button
                            @click="viewMode = 'card'"
                            :class="
                                viewMode === 'card'
                                    ? 'bg-white text-indigo-600 shadow-sm'
                                    : 'text-slate-500 hover:text-slate-700'
                            "
                            class="rounded-lg p-2 transition-all duration-200"
                            title="Mode Card"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                    </div>

                    <button
                        @click="openModal('add')"
                        class="flex items-center space-x-2 rounded-xl bg-indigo-600 px-5 py-2.5 font-bold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Admin</span>
                    </button>
                </div>
            </div>

            <div
                v-if="viewMode === 'table'"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300"
            >
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-xs font-bold tracking-wider text-slate-500 uppercase">
                                <th class="px-6 py-4">Avatar</th>
                                <th class="px-6 py-4">Nama / Role</th>
                                <th class="px-6 py-4">Nomor WhatsApp</th>
                                <th class="px-6 py-4 text-center">Urutan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="admin in admins"
                                :key="admin.id"
                                class="transition-colors duration-150 hover:bg-slate-50/50"
                            >
                                <td class="px-6 py-4">
                                    <div
                                        :class="admin.color"
                                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-black/5 font-['Montserrat'] text-sm font-extrabold"
                                    >
                                        {{ admin.initial }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ admin.name }}
                                    </p>
                                    <p class="text-xs font-semibold text-slate-500">
                                        {{ admin.role }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm font-semibold text-slate-600">+{{ admin.phone }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        @click="toggleStatus(admin)"
                                        :disabled="isProcessing(admin.id)"
                                        class="inline-flex items-center justify-center rounded-md px-3 py-1 text-[10px] leading-none font-extrabold tracking-wider uppercase transition-all duration-200"
                                        :class="[
                                            admin.is_active
                                                ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200',
                                            isProcessing(admin.id) ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : ''
                                        ]"
                                    >
                                        {{ isProcessing(admin.id) ? 'Loading...' : (admin.is_active ? 'Aktif' : 'Non-aktif') }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button
                                            @click="openModal('edit', admin)"
                                            :disabled="isProcessing(admin.id)"
                                            class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600 disabled:opacity-50"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteAdmin(admin.id)"
                                            :disabled="isProcessing(admin.id)"
                                            class="flex items-center justify-center rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600 disabled:bg-slate-100 disabled:text-slate-400"
                                        >
                                            <span v-if="isProcessing(admin.id)" class="text-[10px] font-bold">Loading...</span>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                v-else
                class="grid grid-cols-1 gap-6 transition-all duration-300 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <div
                    v-for="admin in admins"
                    :key="admin.id"
                    class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:border-indigo-300"
                >
                    <div class="mb-4 flex items-start justify-between">
                        <div
                            :class="admin.color"
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border border-black/5 font-['Montserrat'] text-xl font-extrabold shadow-sm transition-transform duration-300 group-hover:scale-110"
                        >
                            {{ admin.initial }}
                        </div>

                        <div>
                            <button
                                @click="toggleStatus(admin)"
                                :disabled="isProcessing(admin.id)"
                                class="inline-flex items-center justify-center rounded-lg px-2.5 py-1 text-[10px] font-extrabold tracking-widest uppercase transition-all duration-200"
                                :class="[
                                    admin.is_active
                                        ? 'border border-emerald-200 bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                                        : 'border border-slate-200 bg-slate-100 text-slate-500 hover:bg-slate-200',
                                    isProcessing(admin.id) ? 'bg-slate-200 text-slate-400 border-slate-300 cursor-not-allowed' : ''
                                ]"
                            >
                                {{ isProcessing(admin.id) ? 'Loading...' : (admin.is_active ? 'Aktif' : 'Off') }}
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h4 class="truncate font-['Montserrat'] text-base font-bold text-slate-800">
                            {{ admin.name }}
                        </h4>
                        <p class="text-xs font-bold tracking-wider text-indigo-600 uppercase">
                            {{ admin.role }}
                        </p>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-t border-slate-50 pt-4">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Nomor WA</span>
                            <span class="font-mono text-sm font-bold tracking-tight text-slate-700">+{{ admin.phone }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <button
                                @click="openModal('edit', admin)"
                                :disabled="isProcessing(admin.id)"
                                class="p-2 text-slate-400 transition-colors hover:text-indigo-600 disabled:opacity-50"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button
                                @click="deleteAdmin(admin.id)"
                                :disabled="isProcessing(admin.id)"
                                class="flex items-center justify-center p-2 text-slate-400 transition-colors hover:text-rose-600 disabled:text-slate-400"
                            >
                                <span v-if="isProcessing(admin.id)" class="text-[9px] font-bold">Loading...</span>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="closeModal"></div>

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
                        class="relative flex w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                    >
                        <div class="flex items-center justify-between border-b border-slate-50 bg-slate-50/50 px-6 py-5">
                            <h3 class="font-['Montserrat'] text-lg font-bold text-slate-800">
                                {{ modalMode === 'add' ? 'Tambah Admin Baru' : 'Edit Kontak Admin' }}
                            </h3>
                            <button
                                @click="closeModal"
                                class="rounded-xl p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-500"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-5 p-6">
                            <div class="grid grid-cols-1 gap-5">
                                <div>
                                    <label class="mb-1.5 ml-1 block text-xs font-bold tracking-widest text-slate-500 uppercase">Nama Lengkap</label>
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        placeholder="Misal: Admin Siska"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                        :class="{ 'border-rose-500': form.errors.name }"
                                    />
                                    <p v-if="form.errors.name" class="mt-1 ml-1 text-xs font-bold text-rose-500">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label class="mb-1.5 ml-1 block text-xs font-bold tracking-widest text-slate-500 uppercase">Jabatan / Role</label>
                                    <input
                                        type="text"
                                        v-model="form.role"
                                        placeholder="Misal: Customer Service"
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                        :class="{ 'border-rose-500': form.errors.role }"
                                    />
                                    <p v-if="form.errors.role" class="mt-1 ml-1 text-xs font-bold text-rose-500">{{ form.errors.role }}</p>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 ml-1 block text-xs font-bold tracking-widest text-slate-500 uppercase">Nomor WhatsApp</label>
                                        <input
                                            type="text"
                                            v-model="form.phone"
                                            placeholder="Contoh: 0812345..."
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{ 'border-rose-500': form.errors.phone }"
                                        />
                                        <p v-if="form.errors.phone" class="mt-1 ml-1 text-xs font-bold text-rose-500">{{ form.errors.phone }}</p>
                                    </div>
                                    <div>
                                        <label class="mb-1.5 ml-1 block text-xs font-bold tracking-widest text-slate-500 uppercase">Inisial (Opsi)</label>
                                        <input
                                            type="text"
                                            v-model="form.initial"
                                            maxlength="2"
                                            placeholder="Max 2 huruf"
                                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 uppercase transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                            :class="{ 'border-rose-500': form.errors.initial }"
                                        />
                                        <p v-if="form.errors.initial" class="mt-1 ml-1 text-xs font-bold text-rose-500">{{ form.errors.initial }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-2 ml-1 block text-xs font-bold tracking-widest text-slate-500 uppercase">Warna Label / Avatar</label>
                                    <div class="flex flex-wrap gap-3">
                                        <button
                                            v-for="opt in colorOptions"
                                            :key="opt.key"
                                            @click="form.color = opt.key"
                                            :class="[
                                                opt.class,
                                                form.color === opt.key ? 'ring-2 ring-indigo-500 ring-offset-2' : '',
                                            ]"
                                            class="h-10 w-10 rounded-xl border border-black/5 transition-all duration-200"
                                            :title="opt.label"
                                        ></button>
                                    </div>
                                    <p v-if="form.errors.color" class="mt-1 ml-1 text-xs font-bold text-rose-500">{{ form.errors.color }}</p>
                                </div>

                                <div class="flex items-center space-x-3 rounded-xl border border-slate-100 bg-slate-50 p-4">
                                    <button
                                        type="button"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="form.is_active ? 'bg-emerald-500' : 'bg-slate-300'"
                                        @click="form.is_active = !form.is_active"
                                    >
                                        <span
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="form.is_active ? 'translate-x-5' : 'translate-x-0'"
                                        />
                                    </button>
                                    <span class="text-sm font-bold tracking-wider text-slate-700 uppercase">Aktifkan Kontak Ini</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 border-t border-slate-50 bg-slate-50/50 px-6 py-5">
                            <button
                                @click="closeModal"
                                :disabled="form.processing"
                                class="px-5 py-2.5 text-sm font-bold text-slate-500 transition-colors hover:text-slate-800 disabled:opacity-50"
                            >
                                Batal
                            </button>
                            <button
                                @click="submitForm"
                                :disabled="form.processing"
                                :class="form.processing ? 'bg-slate-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                                class="rounded-xl px-6 py-2.5 text-sm font-bold text-white shadow-sm transition-all"
                            >
                                <span v-if="form.processing">Loading...</span>
                                <span v-else>Simpan Kontak</span>
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </AdminLayout>
</template>
