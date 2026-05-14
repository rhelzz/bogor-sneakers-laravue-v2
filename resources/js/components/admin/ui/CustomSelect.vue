<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

interface Option {
    label: string;
    value: string | number;
}

const props = defineProps<{
    modelValue: string | number;
    options: Record<string | number, string> | Option[];
    placeholder?: string;
    label?: string;
    error?: string;
}>();

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const containerRef = ref<HTMLElement | null>(null);

const normalizedOptions = computed(() => {
    if (Array.isArray(props.options)) {
        return props.options;
    }
    return Object.entries(props.options).map(([value, label]) => ({
        label,
        value
    }));
});

const selectedLabel = computed(() => {
    const option = normalizedOptions.value.find(opt => opt.value == props.modelValue);
    return option ? option.label : props.placeholder || 'Pilih opsi';
});

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectOption = (option: Option) => {
    emit('update:modelValue', option.value);
    emit('change', option.value);
    isOpen.value = false;
};

const closeDropdown = (e: MouseEvent) => {
    if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
    window.removeEventListener('click', closeDropdown);
});

import { computed } from 'vue';
</script>

<template>
    <div class="space-y-1.5" ref="containerRef">
        <label v-if="label" class="block text-xs font-bold tracking-wider text-slate-500 uppercase ml-1">
            {{ label }}
        </label>
        
        <div class="relative">
            <!-- Trigger -->
            <button
                type="button"
                @click="toggleDropdown"
                class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm transition-all duration-300 ease-in-out focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10"
                :class="[
                    isOpen ? 'border-indigo-500 ring-4 ring-indigo-500/10' : 'hover:border-slate-300',
                    error ? 'border-rose-500 focus:ring-rose-500/10' : ''
                ]"
            >
                <span :class="modelValue ? 'text-slate-800 font-medium' : 'text-slate-400'">
                    {{ selectedLabel }}
                </span>
                <svg 
                    xmlns="http://www.w3.org/2000/svg" 
                    class="h-4 w-4 text-slate-400 transition-transform duration-300 ease-in-out"
                    :class="{ 'rotate-180 text-indigo-500': isOpen }"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                enter-to-class="transform scale-100 opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform scale-100 opacity-100 translate-y-0"
                leave-to-class="transform scale-95 opacity-0 -translate-y-2"
            >
                <div 
                    v-if="isOpen"
                    class="absolute z-50 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white p-1 shadow-xl"
                >
                    <div class="max-h-60 overflow-y-auto no-scrollbar">
                        <button
                            v-for="option in normalizedOptions"
                            :key="option.value"
                            type="button"
                            @click="selectOption(option)"
                            class="flex w-full items-center px-3 py-2 text-sm transition-all duration-200 ease-in-out rounded-lg"
                            :class="[
                                option.value == modelValue 
                                    ? 'bg-indigo-50 text-indigo-700 font-bold' 
                                    : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600'
                            ]"
                        >
                            <span class="flex-1 text-left">{{ option.label }}</span>
                            <svg v-if="option.value == modelValue" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
        
        <p v-if="error" class="ml-1 text-[11px] font-bold text-rose-500 italic">
            * {{ error }}
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
