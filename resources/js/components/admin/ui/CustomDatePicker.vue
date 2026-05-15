<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';

interface Props {
    modelValue: string; // YYYY-MM-DD
    label?: string;
    placeholder?: string;
    maxDate?: string; // YYYY-MM-DD
    error?: string;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const datePickerRef = ref<HTMLElement | null>(null);

// Calendar State
const today = new Date();
const currentMonth = ref(new Date(props.modelValue || today).getMonth());
const currentYear = ref(new Date(props.modelValue || today).getFullYear());

const monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

const daysInMonth = (month: number, year: number) => new Date(year, month + 1, 0).getDate();
const firstDayOfMonth = (month: number, year: number) => new Date(year, month, 1).getDay();

const calendarDays = computed(() => {
    const days = [];
    const totalDays = daysInMonth(currentMonth.value, currentYear.value);
    const firstDay = firstDayOfMonth(currentMonth.value, currentYear.value);

    // Padding for previous month days
    for (let i = 0; i < firstDay; i++) {
        days.push({ day: null, currentMonth: false });
    }

    for (let i = 1; i <= totalDays; i++) {
        days.push({
            day: i,
            currentMonth: true,
            dateString: `${currentYear.value}-${String(currentMonth.value + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`
        });
    }

    return days;
});

const isSelected = (dateString: string) => props.modelValue === dateString;

const isDisabled = (dateString: string) => {
    if (!props.maxDate) return false;
    return new Date(dateString) > new Date(props.maxDate);
};

const selectDate = (dateString: string) => {
    if (isDisabled(dateString)) return;
    emit('update:modelValue', dateString);
    isOpen.value = false;
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value && props.modelValue) {
        const d = new Date(props.modelValue);
        currentMonth.value = d.getMonth();
        currentYear.value = d.getFullYear();
    }
};

const handleClickOutside = (event: MouseEvent) => {
    if (datePickerRef.value && !datePickerRef.value.contains(event.target as Node)) {
        isOpen.value = false;
    }
};

const formatDateDisplay = (dateString: string) => {
    if (!dateString) return '';
    const d = new Date(dateString);
    return `${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});
</script>

<template>
    <div class="w-full" ref="datePickerRef">
        <label v-if="label" class="mb-1.5 ml-1 block text-xs font-bold tracking-widest text-slate-500 uppercase">
            {{ label }}
        </label>
        <div class="relative">
            <button
                type="button"
                @click="toggleDropdown"
                class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-all duration-300 hover:border-indigo-300 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                :class="[
                    error ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500/10' : '',
                    isOpen ? 'border-indigo-500 bg-white ring-4 ring-indigo-500/10' : ''
                ]"
            >
                <div class="flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span :class="{ 'text-slate-400': !modelValue }">
                        {{ modelValue ? formatDateDisplay(modelValue) : (placeholder || 'Pilih tanggal...') }}
                    </span>
                </div>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 text-slate-400 transition-transform duration-300"
                    :class="{ 'rotate-180': isOpen }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Calendar Dropdown -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 translate-y-4 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 translate-y-4 scale-95"
            >
                <div
                    v-if="isOpen"
                    class="absolute z-50 mt-2 w-72 overflow-hidden rounded-2xl border border-slate-100 bg-white p-4 shadow-2xl shadow-slate-200/60"
                >
                    <!-- Header -->
                    <div class="mb-4 flex items-center justify-between">
                        <button @click="prevMonth" type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <h4 class="font-['Montserrat'] text-sm font-bold text-slate-800">
                            {{ monthNames[currentMonth] }} {{ currentYear }}
                        </h4>
                        <button @click="nextMonth" type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Weekdays -->
                    <div class="mb-2 grid grid-cols-7 gap-1 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase">
                        <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                    </div>

                    <!-- Days -->
                    <div class="grid grid-cols-7 gap-1">
                        <div v-for="(item, index) in calendarDays" :key="index" class="aspect-square">
                            <button
                                v-if="item.day"
                                type="button"
                                @click="selectDate(item.dateString!)"
                                :disabled="isDisabled(item.dateString!)"
                                class="flex h-full w-full items-center justify-center rounded-lg text-xs font-bold transition-all duration-200"
                                :class="[
                                    isSelected(item.dateString!)
                                        ? 'bg-indigo-600 text-white shadow-md shadow-indigo-200 scale-110'
                                        : isDisabled(item.dateString!)
                                            ? 'text-slate-200 cursor-not-allowed'
                                            : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-600'
                                ]"
                            >
                                {{ item.day }}
                            </button>
                        </div>
                    </div>

                    <!-- Footer / Today Button -->
                    <div class="mt-4 border-t border-slate-50 pt-3">
                        <button
                            @click="selectDate(new Date().toISOString().split('T')[0])"
                            type="button"
                            class="w-full rounded-xl bg-slate-50 py-2 text-xs font-bold text-indigo-600 transition-colors hover:bg-indigo-50"
                        >
                            Hari Ini
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
        <p v-if="error" class="mt-1 ml-1 text-xs font-bold text-rose-500">
            {{ error }}
        </p>
    </div>
</template>
