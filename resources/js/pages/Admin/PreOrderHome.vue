<template>
    <AdminLayout>
        <Head title="Admin Pre-Order Home" />
        <div class="admin-page">
            <AdminPageHeader
                title="Kelola Pre-Order Home"
                :description="`Pilih produk PO dari katalog, atur slot, status, dan countdown. Maksimal ${visibleLimit} produk bisa visible di Home.`"
            />

            <AdminAlert :message="successMessage" variant="success" />
            <AdminAlert :message="errorMessage" variant="error" />

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <div class="xl:col-span-1">
                    <div class="admin-card admin-card-sticky">
                        <h3
                            class="admin-section-title mb-4 flex items-center gap-2"
                        >
                            <i class="bi bi-hourglass-split text-sm"></i>
                            {{
                                form.id === null
                                    ? 'Tambah Pre-Order'
                                    : 'Edit Pre-Order'
                            }}
                        </h3>

                        <div class="mb-3">
                            <label class="admin-label">Produk Katalog PO</label>
                            <select
                                v-model="form.catalog_id"
                                class="admin-select"
                                :disabled="isSubmitting"
                            >
                                <option :value="null">Pilih produk</option>
                                <option
                                    v-for="option in catalogOptions"
                                    :key="option.id"
                                    :value="option.id"
                                >
                                    {{ option.code }} - {{ option.name }}
                                    {{
                                        option.is_assigned &&
                                        option.assignment_id !== form.id
                                            ? '(sudah dipakai)'
                                            : ''
                                    }}
                                </option>
                            </select>
                        </div>

                        <div
                            v-if="selectedCatalog"
                            class="admin-card-muted mb-3"
                        >
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="selectedCatalog.image_url"
                                    :src="selectedCatalog.image_url"
                                    :alt="selectedCatalog.name"
                                    class="h-12 w-12 rounded-lg object-cover"
                                    loading="lazy"
                                />
                                <div
                                    v-else
                                    class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-200 text-slate-500"
                                >
                                    <i class="bi bi-image"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold">
                                        {{ selectedCatalog.name }}
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ selectedCatalog.brand }} ·
                                        {{ selectedCatalog.collection }}
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        Size {{ selectedCatalog.size_range }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="admin-label">Status</label>
                                <select
                                    v-model="form.status"
                                    class="admin-select"
                                    :disabled="isSubmitting"
                                >
                                    <option value="buka">BUKA</option>
                                    <option value="hampir_habis">
                                        HAMPIR HABIS
                                    </option>
                                    <option value="habis">HABIS</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-1 flex items-center justify-between text-xs tracking-wider text-slate-500"
                                >
                                    <span>BATCH</span>
                                    <span class="text-[10px] text-slate-400"
                                        >(Opsional)</span
                                    >
                                </label>
                                <input
                                    v-model.trim="form.batch_label"
                                    type="text"
                                    maxlength="40"
                                    class="admin-input"
                                    placeholder="Kosongkan jika tidak perlu"
                                    :disabled="isSubmitting"
                                />
                            </div>
                        </div>

                        <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div>
                                <label class="admin-label">Max Slots</label>
                                <input
                                    v-model.number="form.max_slots"
                                    type="number"
                                    min="1"
                                    max="9999"
                                    class="admin-input"
                                    :disabled="isSubmitting"
                                />
                            </div>
                            <div>
                                <label class="admin-label">Filled Slots</label>
                                <input
                                    v-model.number="form.filled_slots"
                                    type="number"
                                    min="0"
                                    :max="Math.max(form.max_slots, 0)"
                                    class="admin-input"
                                    :disabled="isSubmitting"
                                />
                            </div>
                        </div>

                        <div class="admin-card-muted mb-3">
                            <div class="mb-1 flex items-center justify-between">
                                <p class="text-xs font-bold text-slate-700">
                                    Progress Slot
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ formProgress }}%
                                </p>
                            </div>
                            <div
                                class="h-2 overflow-hidden rounded-full bg-slate-200"
                            >
                                <div
                                    class="h-full rounded-full bg-emerald-600 transition-all"
                                    :style="{ width: `${formProgress}%` }"
                                ></div>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-500">
                                Sisa slot: {{ formRemainingSlots }}
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="admin-label">Waktu Tutup PO</label>
                            <input
                                v-model="form.countdown_ends_at"
                                type="datetime-local"
                                class="admin-input"
                                :disabled="isSubmitting"
                            />
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <button
                                    class="admin-btn admin-btn-soft rounded-full px-2.5 py-1 text-[11px]"
                                    type="button"
                                    :disabled="isSubmitting"
                                    @click="applyQuickOffset(1)"
                                >
                                    +1 jam
                                </button>
                                <button
                                    class="admin-btn admin-btn-soft rounded-full px-2.5 py-1 text-[11px]"
                                    type="button"
                                    :disabled="isSubmitting"
                                    @click="applyQuickOffset(6)"
                                >
                                    +6 jam
                                </button>
                                <button
                                    class="admin-btn admin-btn-soft rounded-full px-2.5 py-1 text-[11px]"
                                    type="button"
                                    :disabled="isSubmitting"
                                    @click="applyQuickOffset(24)"
                                >
                                    +1 hari
                                </button>
                                <button
                                    class="admin-btn admin-btn-soft rounded-full px-2.5 py-1 text-[11px]"
                                    type="button"
                                    :disabled="isSubmitting"
                                    @click="applyQuickOffset(72)"
                                >
                                    +3 hari
                                </button>
                                <button
                                    class="admin-btn admin-btn-soft rounded-full px-2.5 py-1 text-[11px]"
                                    type="button"
                                    :disabled="isSubmitting"
                                    @click="applyQuickOffset(168)"
                                >
                                    +7 hari
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="admin-label">Urutan</label>
                            <select
                                v-model.number="form.urutan"
                                class="admin-select"
                                :disabled="isSubmitting"
                            >
                                <option :value="null">Pilih urutan</option>
                                <option
                                    v-for="option in urutanOptions"
                                    :key="option.value"
                                    :value="option.value"
                                    :disabled="option.disabled"
                                >
                                    {{ option.label }}
                                    {{
                                        option.disabled ? '(sudah dipakai)' : ''
                                    }}
                                </option>
                            </select>
                        </div>

                        <button
                            class="admin-btn admin-btn-primary admin-btn-block"
                            :disabled="isSubmitting"
                            @click="submitForm"
                        >
                            <i
                                v-if="!isSubmitting"
                                class="bi"
                                :class="
                                    form.id === null
                                        ? 'bi-plus-circle'
                                        : 'bi-save'
                                "
                            ></i>
                            <i
                                v-else
                                class="bi bi-hourglass-split animate-spin"
                            ></i>
                            {{
                                isSubmitting
                                    ? 'Menyimpan...'
                                    : form.id === null
                                      ? 'Tambah Pre-Order'
                                      : 'Simpan Perubahan'
                            }}
                        </button>

                        <button
                            v-if="form.id !== null"
                            class="admin-btn admin-btn-soft admin-btn-block mt-2"
                            :disabled="isSubmitting"
                            @click="resetForm"
                        >
                            Batal Edit
                        </button>
                    </div>
                </div>

                <div class="xl:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="admin-section-title flex items-center gap-2">
                            <i class="bi bi-grid text-sm"></i>
                            Daftar Pre-Order ({{ assignments.length }})
                        </h3>
                    </div>

                    <div
                        v-if="assignments.length === 0"
                        class="admin-empty-state"
                    >
                        <i
                            class="bi bi-hourglass mb-2 block text-4xl text-slate-400"
                        ></i>
                        <p class="mb-1 text-sm font-semibold text-slate-700">
                            Belum ada produk pre-order untuk Home
                        </p>
                        <p class="admin-muted-text">
                            Tambahkan produk PO dari form di sebelah kiri.
                        </p>
                    </div>

                    <div v-else class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <article
                            v-for="assignment in sortedAssignments"
                            :key="assignment.id"
                            class="rounded-xl border border-slate-200 bg-white"
                        >
                            <div
                                class="relative aspect-16/11 overflow-hidden rounded-t-xl bg-slate-100"
                            >
                                <img
                                    v-if="assignment.catalog?.image_url"
                                    :src="assignment.catalog.image_url"
                                    :alt="assignment.catalog.name"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-slate-400"
                                >
                                    <i class="bi bi-image text-4xl"></i>
                                </div>

                                <span
                                    class="absolute top-2.5 left-2.5 rounded-full px-3 py-1 text-[10px] tracking-wider uppercase"
                                    :class="statusBadgeClass(assignment.status)"
                                >
                                    {{ assignment.status_label }}
                                </span>

                                <button
                                    type="button"
                                    @click="toggleAssignmentVisible(assignment)"
                                    :disabled="busyRowId === assignment.id"
                                    class="absolute top-2.5 right-2.5 z-10 rounded-full px-3 py-1.5 text-xs font-bold transition-all disabled:cursor-not-allowed disabled:opacity-60"
                                    :class="
                                        assignment.is_visible
                                            ? 'cursor-pointer bg-emerald-600 text-white hover:bg-emerald-500'
                                            : 'cursor-pointer bg-slate-600 text-white hover:bg-slate-500'
                                    "
                                >
                                    {{
                                        assignment.is_visible
                                            ? 'Visible'
                                            : 'Hidden'
                                    }}
                                </button>
                            </div>

                            <div class="space-y-3 p-3.5">
                                <div>
                                    <p
                                        class="text-[10px] tracking-wider text-hai uppercase"
                                    >
                                        {{ assignment.catalog?.brand ?? '-' }} ·
                                        {{
                                            assignment.catalog?.collection ??
                                            '-'
                                        }}
                                    </p>
                                    <p class="font-heading text-base font-bold">
                                        {{ assignment.catalog?.name ?? '-' }}
                                    </p>
                                    <p class="text-xs text-hai">
                                        {{ assignment.catalog?.code ?? '-' }} ·
                                        Size
                                        {{
                                            assignment.catalog?.size_range ??
                                            '-'
                                        }}
                                    </p>
                                </div>

                                <div class="admin-card-muted">
                                    <div
                                        class="mb-1 flex items-center justify-between"
                                    >
                                        <p
                                            class="text-xs font-bold text-slate-700"
                                        >
                                            Slot Progress
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{ assignment.progress }}%
                                        </p>
                                    </div>
                                    <div
                                        class="h-2 overflow-hidden rounded-full bg-slate-200"
                                    >
                                        <div
                                            class="h-full rounded-full transition-all"
                                            :class="
                                                assignment.progress >= 90
                                                    ? 'bg-amber-500'
                                                    : 'bg-emerald-600'
                                            "
                                            :style="{
                                                width: `${assignment.progress}%`,
                                            }"
                                        ></div>
                                    </div>
                                    <div
                                        class="mt-1 flex items-center justify-between text-[11px] text-slate-500"
                                    >
                                        <span>
                                            Filled
                                            {{ assignment.filled_slots }}/{{
                                                assignment.max_slots
                                            }}
                                        </span>
                                        <span
                                            >Sisa
                                            {{
                                                assignment.remaining_slots
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <div
                                    class="grid grid-cols-3 gap-2 text-xs text-slate-500"
                                >
                                    <div
                                        class="rounded-lg border border-slate-200 bg-slate-50 p-2"
                                    >
                                        <p
                                            class="mb-1 text-[10px] tracking-wider text-hai/80 uppercase"
                                        >
                                            Urutan
                                        </p>
                                        <p class="font-bold text-slate-700">
                                            #{{
                                                toSafeUrutan(assignment.urutan)
                                            }}
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-lg border border-slate-200 bg-slate-50 p-2"
                                    >
                                        <p
                                            class="mb-1 text-[10px] tracking-wider text-hai/80 uppercase"
                                        >
                                            Batch
                                        </p>
                                        <p class="font-bold text-slate-700">
                                            {{ assignment.batch_label || '-' }}
                                        </p>
                                    </div>
                                    <div
                                        class="rounded-lg border border-slate-200 bg-slate-50 p-2"
                                    >
                                        <p
                                            class="mb-1 text-[10px] tracking-wider text-hai/80 uppercase"
                                        >
                                            Tutup PO
                                        </p>
                                        <p class="font-bold text-slate-700">
                                            {{
                                                formatDateTime(
                                                    assignment.countdown_ends_at,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        class="admin-btn admin-btn-secondary flex-1"
                                        :disabled="busyRowId === assignment.id"
                                        @click="startEdit(assignment)"
                                    >
                                        Edit
                                    </button>
                                    <a
                                        v-if="assignment.catalog?.route_key"
                                        :href="`/katalog/${assignment.catalog.route_key}`"
                                        target="_blank"
                                        class="admin-btn admin-btn-soft"
                                    >
                                        Detail
                                    </a>
                                    <button
                                        class="admin-btn admin-btn-danger disabled:cursor-not-allowed disabled:opacity-60"
                                        :disabled="busyRowId === assignment.id"
                                        @click="deleteAssignment(assignment)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AdminAlert from '@/components/admin/AdminAlert.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { useAdminPreOrderHomeManager } from '@/composables/Admin/preOrderHome/useAdminPreOrderHomeManager';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type {
    CatalogOption,
    PreOrderAssignment,
} from '@/types/Admin/preOrderHome';

const props = defineProps<{
    assignments: PreOrderAssignment[];
    catalogOptions: CatalogOption[];
    visibleCount: number;
    visibleLimit: number;
}>();

const {
    assignments,
    catalogOptions,
    visibleLimit,
    successMessage,
    errorMessage,
    isSubmitting,
    busyRowId,
    form,
    sortedAssignments,
    selectedCatalog,
    formProgress,
    formRemainingSlots,
    urutanOptions,
    applyQuickOffset,
    submitForm,
    resetForm,
    toggleAssignmentVisible,
    startEdit,
    deleteAssignment,
    toSafeUrutan,
    formatDateTime,
    statusBadgeClass,
} = useAdminPreOrderHomeManager({
    initialAssignments: props.assignments,
    initialCatalogOptions: props.catalogOptions,
    initialVisibleCount: props.visibleCount,
    visibleLimit: props.visibleLimit,
});
</script>
