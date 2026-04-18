<template>
    <AdminLayout>
        <Head title="Admin WhatsApp Admins" />
        <div class="admin-page">
            <AdminPageHeader
                title="Kelola WhatsApp Admin"
                description="Atur nomor WhatsApp panel floating secara dinamis dan tampilkan beberapa admin sekaligus."
            />

            <AdminAlert :message="successMessage" variant="success" />
            <AdminAlert :message="errorMessage" variant="error" />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div class="admin-card admin-card-sticky">
                        <h3 class="admin-section-title mb-4 flex items-center gap-2">
                            <i class="bi bi-whatsapp text-base"></i>
                            {{ editId ? 'Edit Kontak Admin' : 'Tambah Kontak Admin' }}
                        </h3>

                        <div class="mb-4">
                            <label class="admin-label">Nama Admin</label>
                            <input
                                v-model.trim="form.name"
                                type="text"
                                maxlength="120"
                                placeholder="Contoh: Rizky - Admin"
                                class="admin-input"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="admin-label">Role / Layanan</label>
                            <input
                                v-model.trim="form.role"
                                type="text"
                                maxlength="160"
                                placeholder="Contoh: PO / Order / Ketersediaan"
                                class="admin-input"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="admin-label">Nomor WhatsApp</label>
                            <input
                                v-model.trim="form.phone"
                                type="text"
                                maxlength="32"
                                placeholder="Contoh: 081234567890 atau 6281234567890"
                                class="admin-input"
                            />
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="admin-label">Inisial</label>
                                <input
                                    v-model.trim="form.initial"
                                    type="text"
                                    maxlength="2"
                                    placeholder="R"
                                    class="admin-input"
                                />
                            </div>

                            <div>
                                <label class="admin-label">Urutan</label>
                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    max="9999"
                                    class="admin-input"
                                />
                            </div>
                        </div>

                        <div class="mt-4 mb-4">
                            <label class="admin-label">Warna Avatar</label>
                            <select
                                v-model="form.color_key"
                                class="admin-select"
                            >
                                <option
                                    v-for="option in colorOptions"
                                    :key="option.key"
                                    :value="option.key"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                <span
                                    :class="[
                                        'flex h-7 w-7 items-center justify-center rounded-full font-bold',
                                        selectedColorClass,
                                    ]"
                                >
                                    {{ previewInitial }}
                                </span>
                                <span>Preview avatar kontak</span>
                            </div>
                        </div>

                        <div
                            class="admin-card-muted mb-4 flex items-center justify-between"
                        >
                            <span class="admin-muted-text"
                                >Tampilkan di panel publik</span
                            >
                            <button
                                type="button"
                                @click="form.is_active = !form.is_active"
                                :class="[
                                    'flex h-7 w-12 items-center rounded-full px-1 transition-all',
                                    form.is_active
                                        ? 'bg-emerald-600'
                                        : 'bg-slate-300',
                                ]"
                            >
                                <span
                                    :class="[
                                        'h-5 w-5 rounded-full bg-white transition-all',
                                        form.is_active
                                            ? 'translate-x-5'
                                            : 'translate-x-0',
                                    ]"
                                ></span>
                            </button>
                        </div>

                        <button
                            @click="submitForm"
                            :disabled="isSubmitting"
                            class="admin-btn admin-btn-primary admin-btn-block"
                        >
                            <i v-if="!isSubmitting" class="bi bi-save"></i>
                            <i
                                v-else
                                class="bi bi-hourglass-split animate-spin"
                            ></i>
                            {{
                                isSubmitting
                                    ? 'Menyimpan...'
                                    : editId
                                      ? 'Simpan Perubahan'
                                      : 'Tambah Admin'
                            }}
                        </button>

                        <button
                            v-if="editId"
                            @click="resetForm"
                            class="admin-btn admin-btn-soft admin-btn-block mt-2"
                        >
                            Batal Edit
                        </button>

                        <div class="admin-card-muted mt-4">
                            <p class="admin-muted-text leading-relaxed">
                                Kontak aktif {{ activeCount }}/{{ admins.length }}.
                                Data aktif akan otomatis tampil di WhatsApp panel
                                pada halaman publik.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="admin-section-title flex items-center gap-2">
                            <i class="bi bi-people text-sm"></i>
                            Daftar Admin ({{ admins.length }})
                        </h3>
                    </div>

                    <div v-if="admins.length === 0" class="admin-empty-state">
                        <i
                            class="bi bi-person-plus mb-2 block text-4xl text-slate-400"
                        ></i>
                        <p class="mb-1 text-sm font-semibold text-slate-700">
                            Belum ada kontak admin WhatsApp
                        </p>
                        <p class="admin-muted-text">
                            Tambahkan kontak pertama lewat form di sebelah kiri.
                        </p>
                    </div>

                    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <article
                            v-for="admin in sortedAdmins"
                            :key="admin.id"
                            class="rounded-xl border border-slate-200 bg-white p-4"
                        >
                            <div class="mb-4 flex items-start gap-3">
                                <span
                                    :class="[
                                        'flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-sm font-bold',
                                        admin.color,
                                    ]"
                                >
                                    {{ admin.initial }}
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900">
                                        {{ admin.name }}
                                    </p>
                                    <p class="truncate text-xs text-slate-500">
                                        {{ admin.role }}
                                    </p>
                                    <p class="mt-1 text-xs font-medium text-slate-700">
                                        +{{ admin.phone }}
                                    </p>
                                </div>

                                <span
                                    :class="[
                                        'admin-chip',
                                        admin.is_active
                                            ? 'admin-chip-primary'
                                            : 'admin-chip-muted',
                                    ]"
                                >
                                    {{ admin.is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <div class="mb-3 text-[11px] text-slate-500">
                                Urutan tampil: {{ admin.sort_order }}
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a
                                    :href="`https://wa.me/${admin.phone}`"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="admin-btn admin-btn-soft"
                                >
                                    Buka WA
                                </a>

                                <button
                                    @click="toggleActive(admin)"
                                    :disabled="busyRowId === admin.id"
                                    class="admin-btn admin-btn-secondary"
                                >
                                    {{ admin.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>

                                <button
                                    @click="startEdit(admin)"
                                    class="admin-btn admin-btn-secondary"
                                >
                                    Edit
                                </button>

                                <button
                                    @click="removeAdmin(admin)"
                                    :disabled="busyRowId === admin.id"
                                    class="admin-btn admin-btn-danger"
                                >
                                    Hapus
                                </button>
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
import type {
    WhatsappAdminColorKey,
    WhatsappAdminItem,
    WhatsappColorOption,
} from '@/types/whatsapp-admin';

const props = defineProps({
    admins: {
        type: Array as () => WhatsappAdminItem[],
        default: () => [],
    },
    colorOptions: {
        type: Array as () => WhatsappColorOption[],
        default: () => [],
    },
});

type ApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

type ApiUpsertResponse = {
    message: string;
    admin: WhatsappAdminItem;
};

type ApiDeleteResponse = {
    message: string;
    id: number;
};

const admins = ref<WhatsappAdminItem[]>(props.admins);
const colorOptions = ref<WhatsappColorOption[]>(props.colorOptions);

const editId = ref<number | null>(null);
const busyRowId = ref<number | null>(null);
const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const defaultColorKey = computed<WhatsappAdminColorKey>(() => {
    return colorOptions.value[0]?.key ?? 'matcha';
});

const form = reactive({
    name: '',
    role: '',
    phone: '',
    initial: '',
    color_key: defaultColorKey.value,
    is_active: true,
    sort_order: 0,
});

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

const sortedAdmins = computed(() => {
    return [...admins.value].sort((a, b) => {
        if (a.sort_order === b.sort_order) {
            return a.id - b.id;
        }

        return a.sort_order - b.sort_order;
    });
});

const activeCount = computed(() => {
    return admins.value.filter((admin) => admin.is_active).length;
});

const selectedColorClass = computed(() => {
    const selected = colorOptions.value.find(
        (option) => option.key === form.color_key,
    );

    return selected?.class ?? 'bg-slate-200 text-slate-700';
});

const previewInitial = computed(() => {
    const custom = form.initial.trim();

    if (custom !== '') {
        return custom.slice(0, 2).toUpperCase();
    }

    const fallback = form.name.trim();

    if (fallback === '') {
        return 'A';
    }

    return fallback.slice(0, 1).toUpperCase();
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

const nextSortOrder = () => {
    if (admins.value.length === 0) {
        return 0;
    }

    return Math.max(...admins.value.map((admin) => admin.sort_order)) + 1;
};

const resetForm = () => {
    editId.value = null;
    form.name = '';
    form.role = '';
    form.phone = '';
    form.initial = '';
    form.color_key = defaultColorKey.value;
    form.is_active = true;
    form.sort_order = nextSortOrder();
};

const resolvePayload = (
    source: {
        name: string;
        role: string;
        phone: string;
        initial: string;
        color_key: WhatsappAdminColorKey;
        is_active: boolean;
        sort_order: number;
    } = form,
) => {
    return {
        name: source.name.trim(),
        role: source.role.trim(),
        phone: source.phone.trim(),
        initial: source.initial.trim(),
        color: source.color_key,
        is_active: source.is_active,
        sort_order: Number(source.sort_order),
    };
};

const startEdit = (admin: WhatsappAdminItem) => {
    editId.value = admin.id;
    form.name = admin.name;
    form.role = admin.role;
    form.phone = admin.phone;
    form.initial = admin.initial;
    form.color_key = admin.color_key;
    form.is_active = admin.is_active;
    form.sort_order = admin.sort_order;
};

const submitForm = async () => {
    if (form.name.trim() === '' || form.role.trim() === '' || form.phone.trim() === '') {
        errorMessage.value = 'Nama, role, dan nomor WhatsApp wajib diisi.';

        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const payload = resolvePayload();

        if (editId.value) {
            const data = await requestJson<ApiUpsertResponse>(
                `/admin/whatsapp-admins/${editId.value}`,
                {
                    method: 'PUT',
                    body: JSON.stringify(payload),
                },
            );

            admins.value = admins.value.map((admin) => {
                if (admin.id !== editId.value) {
                    return admin;
                }

                return data.admin;
            });

            successMessage.value = data.message;
        } else {
            const data = await requestJson<ApiUpsertResponse>(
                '/admin/whatsapp-admins',
                {
                    method: 'POST',
                    body: JSON.stringify(payload),
                },
            );

            admins.value = [...admins.value, data.admin];
            successMessage.value = data.message;
        }

        resetForm();
    } catch (error) {
        const apiError = error as { payload?: ApiErrorResponse };
        const firstError = Object.values(
            apiError.payload?.errors ?? {},
        )?.[0]?.[0];

        errorMessage.value =
            firstError ||
            apiError.payload?.message ||
            'Gagal menyimpan kontak WhatsApp admin.';
    } finally {
        isSubmitting.value = false;
    }
};

const toggleActive = async (admin: WhatsappAdminItem) => {
    busyRowId.value = admin.id;
    errorMessage.value = '';

    const oldState = admin.is_active;
    admin.is_active = !oldState;

    try {
        const payload = resolvePayload({
            name: admin.name,
            role: admin.role,
            phone: admin.phone,
            initial: admin.initial,
            color_key: admin.color_key,
            is_active: admin.is_active,
            sort_order: admin.sort_order,
        });

        const data = await requestJson<ApiUpsertResponse>(
            `/admin/whatsapp-admins/${admin.id}`,
            {
                method: 'PUT',
                body: JSON.stringify(payload),
            },
        );

        admins.value = admins.value.map((item) => {
            if (item.id !== admin.id) {
                return item;
            }

            return data.admin;
        });

        successMessage.value = data.message;
    } catch (error) {
        admin.is_active = oldState;

        const apiError = error as { payload?: ApiErrorResponse };
        errorMessage.value =
            apiError.payload?.message || 'Gagal memperbarui status kontak.';
    } finally {
        busyRowId.value = null;
    }
};

const removeAdmin = async (admin: WhatsappAdminItem) => {
    if (!confirm('Hapus kontak WhatsApp admin ini?')) {
        return;
    }

    busyRowId.value = admin.id;
    errorMessage.value = '';

    const oldAdmins = [...admins.value];
    admins.value = admins.value.filter((item) => item.id !== admin.id);

    try {
        const data = await requestJson<ApiDeleteResponse>(
            `/admin/whatsapp-admins/${admin.id}`,
            {
                method: 'DELETE',
            },
        );

        successMessage.value = data.message;

        if (editId.value === admin.id) {
            resetForm();
        }
    } catch (error) {
        admins.value = oldAdmins;

        const apiError = error as { payload?: ApiErrorResponse };
        errorMessage.value =
            apiError.payload?.message || 'Gagal menghapus kontak admin.';
    } finally {
        busyRowId.value = null;
    }
};

resetForm();
</script>
