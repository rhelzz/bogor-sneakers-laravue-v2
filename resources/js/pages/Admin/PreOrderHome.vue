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
import { computed, reactive, ref } from 'vue';

import AdminAlert from '@/components/admin/AdminAlert.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

type PreOrderStatus = 'buka' | 'hampir_habis' | 'habis';

type CatalogOption = {
    id: number;
    name: string;
    code: string;
    brand: string;
    collection: string;
    size_range: string;
    image_url: string | null;
    is_assigned: boolean;
    assignment_id: number | null;
};

type AssignmentCatalog = {
    id: number;
    name: string;
    code: string;
    brand: string;
    collection: string;
    size_range: string;
    image_url: string | null;
    route_key: string;
};

type PreOrderAssignment = {
    id: number;
    catalog_id: number;
    status: PreOrderStatus;
    status_label: string;
    is_visible: boolean;
    max_slots: number;
    filled_slots: number;
    remaining_slots: number;
    progress: number;
    countdown_ends_at: string | null;
    batch_label: string | null;
    urutan: number;
    catalog: AssignmentCatalog | null;
    created_at: string | null;
    updated_at: string | null;
};

type ApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

type ApiUpsertResponse = {
    message: string;
    assignment: PreOrderAssignment;
    visible_count: number;
};

type ApiDeleteResponse = {
    message: string;
    id: number;
    visible_count: number;
};

const props = defineProps<{
    assignments: PreOrderAssignment[];
    catalogOptions: CatalogOption[];
    visibleCount: number;
    visibleLimit: number;
}>();

const assignments = ref<PreOrderAssignment[]>([...props.assignments]);
const catalogOptions = ref<CatalogOption[]>([...props.catalogOptions]);
const visibleCount = ref(Number(props.visibleCount));
const visibleLimit = Number(props.visibleLimit);

const successMessage = ref('');
const errorMessage = ref('');
const isSubmitting = ref(false);
const busyRowId = ref<number | null>(null);
const editWasVisible = ref(false);

const form = reactive({
    id: null as number | null,
    catalog_id: null as number | null,
    status: 'buka' as PreOrderStatus,
    is_visible: false,
    max_slots: 24,
    filled_slots: 0,
    countdown_ends_at: '',
    batch_label: '' as string | null,
    urutan: 1,
});

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

const toSafeUrutan = (value: unknown): number => {
    const parsed = Number(value);

    if (!Number.isInteger(parsed) || parsed < 1) {
        return 1;
    }

    return parsed;
};

const sortedAssignments = computed(() => {
    return [...assignments.value].sort((a, b) => {
        const orderCompare = toSafeUrutan(a.urutan) - toSafeUrutan(b.urutan);

        if (orderCompare !== 0) {
            return orderCompare;
        }

        return a.id - b.id;
    });
});

const selectedCatalog = computed(() => {
    if (form.catalog_id === null) {
        return null;
    }

    return (
        catalogOptions.value.find((item) => item.id === form.catalog_id) ?? null
    );
});

const formProgress = computed(() => {
    const maxSlots = Math.max(Number(form.max_slots) || 0, 1);
    const filledSlots = Math.max(Number(form.filled_slots) || 0, 0);

    return Math.max(
        0,
        Math.min(100, Math.round((filledSlots / maxSlots) * 100)),
    );
});

const formRemainingSlots = computed(() => {
    const maxSlots = Math.max(Number(form.max_slots) || 0, 0);
    const filledSlots = Math.max(Number(form.filled_slots) || 0, 0);

    return Math.max(maxSlots - filledSlots, 0);
});

const canEnableVisible = computed(() => {
    if (form.is_visible) {
        return true;
    }

    if (editWasVisible.value) {
        return true;
    }

    return visibleCount.value < visibleLimit;
});

const usedUrutanValues = computed(() => {
    return Array.from(
        new Set(
            assignments.value
                .filter((item) => item.id !== form.id)
                .map((item) => toSafeUrutan(item.urutan)),
        ),
    );
});

const urutanOptions = computed(() => {
    const highestUrutan = assignments.value.reduce(
        (max, item) => Math.max(max, toSafeUrutan(item.urutan)),
        0,
    );
    const maxUrutan = Math.max(10, highestUrutan + 1);

    const options = [];

    for (let i = 1; i <= maxUrutan; i++) {
        options.push({
            value: i,
            label: `Urutan ${i}`,
            disabled: usedUrutanValues.value.includes(i),
        });
    }

    return options;
});

const buildHeaders = (isJsonBody: boolean) => {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    if (isJsonBody) {
        headers['Content-Type'] = 'application/json';
    }

    return headers;
};

const requestJson = async <T,>(url: string, init: RequestInit = {}) => {
    const isFormData = init.body instanceof FormData;

    const response = await fetch(url, {
        credentials: 'same-origin',
        ...init,
        headers: {
            ...buildHeaders(!isFormData),
            ...(init.headers ?? {}),
        },
    });

    const payload = await response.json().catch(() => null);

    if (!response.ok) {
        throw {
            status: response.status,
            payload,
        };
    }

    return payload as T;
};

const normalizeApiError = (error: unknown, fallback: string) => {
    const apiError = error as { payload?: ApiErrorResponse };

    if (apiError.payload?.errors) {
        const firstError = Object.values(apiError.payload.errors)[0]?.[0];

        if (firstError) {
            return firstError;
        }
    }

    return apiError.payload?.message || fallback;
};

const flashSuccess = (message: string) => {
    successMessage.value = message;
    errorMessage.value = '';
};

const flashError = (message: string) => {
    errorMessage.value = message;
    successMessage.value = '';
};

const toLocalDatetimeInput = (isoValue?: string | null) => {
    if (!isoValue) {
        return '';
    }

    const date = new Date(isoValue);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    const pad = (value: number) => String(value).padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

const toIsoFromLocalDatetime = (localValue: string) => {
    const date = new Date(localValue);

    if (Number.isNaN(date.getTime())) {
        return null;
    }

    return date.toISOString();
};

const formatDateTime = (isoValue?: string | null) => {
    if (!isoValue) {
        return '-';
    }

    const date = new Date(isoValue);

    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
};

const statusBadgeClass = (status: PreOrderStatus) => {
    if (status === 'habis') {
        return 'bg-sumi text-washi shadow-sm';
    }

    if (status === 'hampir_habis') {
        return 'bg-amber-100 text-amber-700 shadow-sm';
    }

    return 'bg-matcha text-washi shadow-sm';
};

const syncCatalogOption = (
    catalogId: number,
    assignmentId: number | null,
    isAssigned: boolean,
) => {
    const index = catalogOptions.value.findIndex(
        (item) => item.id === catalogId,
    );

    if (index === -1) {
        return;
    }

    const next = [...catalogOptions.value];
    next[index] = {
        ...next[index],
        is_assigned: isAssigned,
        assignment_id: assignmentId,
    };

    catalogOptions.value = next;
};

const upsertAssignment = (assignment: PreOrderAssignment) => {
    const next = [...assignments.value];
    const index = next.findIndex((item) => item.id === assignment.id);

    let previousCatalogId: number | null = null;

    if (index === -1) {
        next.unshift(assignment);
    } else {
        previousCatalogId = next[index].catalog_id;
        next[index] = assignment;
    }

    assignments.value = next;

    if (
        previousCatalogId !== null &&
        previousCatalogId !== assignment.catalog_id
    ) {
        syncCatalogOption(previousCatalogId, null, false);
    }

    syncCatalogOption(assignment.catalog_id, assignment.id, true);
};

const applyQuickOffset = (hours: number) => {
    const target = new Date(Date.now() + hours * 60 * 60 * 1000);
    form.countdown_ends_at = toLocalDatetimeInput(target.toISOString());
};

const resetForm = () => {
    const nextUrutan =
        assignments.value.length === 0
            ? 1
            : assignments.value.reduce(
                  (max, item) => Math.max(max, toSafeUrutan(item.urutan)),
                  0,
              ) + 1;

    form.id = null;
    form.catalog_id = null;
    form.status = 'buka';
    form.is_visible = false;
    form.max_slots = 24;
    form.filled_slots = 0;
    form.countdown_ends_at = toLocalDatetimeInput(
        new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString(),
    );
    form.batch_label = null;
    form.urutan = nextUrutan;

    editWasVisible.value = false;
};

const toggleAssignmentVisible = async (assignment: PreOrderAssignment) => {
    const newVisibleState = !assignment.is_visible;

    if (
        newVisibleState &&
        visibleCount.value >= visibleLimit &&
        !assignment.is_visible
    ) {
        flashError(`Maksimal ${visibleLimit} produk pre-order bisa visible.`);

        return;
    }

    busyRowId.value = assignment.id;

    try {
        const response = await requestJson<ApiUpsertResponse>(
            `/admin/pre-order-home/${assignment.id}`,
            {
                method: 'PUT',
                body: JSON.stringify({
                    catalog_id: assignment.catalog_id,
                    status: assignment.status,
                    is_visible: newVisibleState,
                    max_slots: assignment.max_slots,
                    filled_slots: assignment.filled_slots,
                    countdown_ends_at: assignment.countdown_ends_at,
                    batch_label: assignment.batch_label,
                    urutan: assignment.urutan,
                }),
            },
        );

        upsertAssignment(response.assignment);
        visibleCount.value = Number(response.visible_count);
        flashSuccess(response.message);
    } catch (error) {
        flashError(normalizeApiError(error, 'Gagal mengubah status visible.'));
    } finally {
        busyRowId.value = null;
    }
};

const submitForm = async () => {
    if (form.catalog_id === null) {
        flashError('Produk katalog wajib dipilih.');

        return;
    }

    const maxSlots = Number(form.max_slots);
    const filledSlots = Number(form.filled_slots);

    if (!Number.isInteger(maxSlots) || maxSlots < 1) {
        flashError('Maksimal slot minimal 1.');

        return;
    }

    if (!Number.isInteger(filledSlots) || filledSlots < 0) {
        flashError('Slot terisi minimal 0.');

        return;
    }

    if (filledSlots > maxSlots) {
        flashError('Slot terisi tidak boleh melebihi maksimal slot.');

        return;
    }

    if (form.countdown_ends_at.trim() === '') {
        flashError('Waktu tutup PO wajib diisi.');

        return;
    }

    const countdownIso = toIsoFromLocalDatetime(form.countdown_ends_at);

    if (!countdownIso) {
        flashError('Format waktu tutup PO tidak valid.');

        return;
    }

    if (new Date(countdownIso).getTime() <= Date.now()) {
        flashError('Waktu tutup PO harus lebih besar dari waktu saat ini.');

        return;
    }

    if (form.is_visible && !canEnableVisible.value) {
        flashError(`Maksimal ${visibleLimit} produk pre-order bisa visible.`);

        return;
    }

    if (form.urutan < 1) {
        flashError('Urutan wajib dipilih.');

        return;
    }

    const payload = {
        catalog_id: form.catalog_id,
        status: form.status,
        is_visible: form.is_visible,
        max_slots: maxSlots,
        filled_slots: filledSlots,
        countdown_ends_at: countdownIso,
        batch_label: form.batch_label || null,
        urutan: Number(form.urutan),
    };

    isSubmitting.value = true;
    errorMessage.value = '';

    try {
        const response = form.id
            ? await requestJson<ApiUpsertResponse>(
                  `/admin/pre-order-home/${form.id}`,
                  {
                      method: 'PUT',
                      body: JSON.stringify(payload),
                  },
              )
            : await requestJson<ApiUpsertResponse>('/admin/pre-order-home', {
                  method: 'POST',
                  body: JSON.stringify(payload),
              });

        upsertAssignment(response.assignment);
        visibleCount.value = Number(response.visible_count);
        flashSuccess(response.message);
        resetForm();
    } catch (error) {
        flashError(
            normalizeApiError(error, 'Gagal menyimpan data pre-order Home.'),
        );
    } finally {
        isSubmitting.value = false;
    }
};

const startEdit = (assignment: PreOrderAssignment) => {
    form.id = assignment.id;
    form.catalog_id = assignment.catalog_id;
    form.status = assignment.status;
    form.is_visible = assignment.is_visible;
    form.max_slots = assignment.max_slots;
    form.filled_slots = assignment.filled_slots;
    form.countdown_ends_at = toLocalDatetimeInput(assignment.countdown_ends_at);
    form.batch_label = assignment.batch_label;
    form.urutan = toSafeUrutan(assignment.urutan);

    editWasVisible.value = assignment.is_visible;
    errorMessage.value = '';
    successMessage.value = '';
};

const deleteAssignment = async (assignment: PreOrderAssignment) => {
    const approved = window.confirm(
        `Hapus pre-order untuk "${assignment.catalog?.name ?? 'produk ini'}"?`,
    );

    if (!approved) {
        return;
    }

    busyRowId.value = assignment.id;

    try {
        const response = await requestJson<ApiDeleteResponse>(
            `/admin/pre-order-home/${assignment.id}`,
            {
                method: 'DELETE',
            },
        );

        assignments.value = assignments.value.filter(
            (item) => item.id !== response.id,
        );
        syncCatalogOption(assignment.catalog_id, null, false);
        visibleCount.value = Number(response.visible_count);

        if (form.id === response.id) {
            resetForm();
        }

        flashSuccess(response.message);
    } catch (error) {
        flashError(normalizeApiError(error, 'Gagal menghapus pre-order Home.'));
    } finally {
        busyRowId.value = null;
    }
};

resetForm();
</script>
