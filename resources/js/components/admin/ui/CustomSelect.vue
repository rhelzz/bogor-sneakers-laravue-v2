<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

interface Props {
    modelValue: string | number;
    options: Record<string, string> | Array<{ value: string | number, label: string }>;
    label?: string;
    placeholder?: string;
    error?: string;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const selectRef = ref<HTMLElement | null>(null);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectOption = (value: string | number) => {
    emit('update:modelValue', value);
    isOpen.value = false;
};

const handleClickOutside = (event: MouseEvent) => {
    if (selectRef.value && !selectRef.value.contains(event.target as Node)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutside);
});

const getLabel = (value: string | number) => {
    if (Array.isArray(props.options)) {
        const opt = props.options.find(o => o.value === value);
        return opt ? opt.label : value;
    } else {
        return props.options[value] || value;
    }
};

const normalizedOptions = () => {
    if (Array.isArray(props.options)) {
        return props.options;
    } else {
        return Object.entries(props.options).map(([value, label]) => ({ value, label }));
    }
};
</script>

<template>
    <div class="w-full" ref="selectRef">
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
                <span :class="{ 'text-slate-400': !modelValue && placeholder }">
                    {{ modelValue ? getLabel(modelValue) : (placeholder || 'Pilih opsi...') }}
                </span>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-slate-400 transition-transform duration-300"
                    :class="{ 'rotate-180': isOpen }"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-2 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 translate-y-2 scale-95"
            >
                <div
                    v-if="isOpen"
                    class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl border border-slate-100 bg-white shadow-xl shadow-slate-200/50"
                >
                    <div class="max-h-60 overflow-y-auto p-1.5 no-scrollbar">
                        <button
                            v-for="option in normalizedOptions()"
                            :key="option.value"
                            type="button"
                            @click="selectOption(option.value)"
                            class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-semibold transition-all duration-200"
                            :class="[
                                modelValue === option.value
                                    ? 'bg-indigo-50 text-indigo-600'
                                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                            ]"
                        >
                            {{ option.label }}
                            <svg
                                v-if="modelValue === option.value"
                                xmlns="http://www.w3.org/2000/svg"
                                class="ml-auto h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
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

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
