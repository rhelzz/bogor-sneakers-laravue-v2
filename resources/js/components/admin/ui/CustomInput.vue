<script setup lang="ts">
interface Props {
    modelValue: string | number;
    label?: string;
    placeholder?: string;
    type?: string;
    error?: string;
}

defineProps<Props>();
defineEmits(['update:modelValue']);
</script>

<template>
    <div class="w-full">
        <label v-if="label" class="mb-1.5 ml-1 block text-xs font-bold tracking-widest text-slate-500 uppercase">
            {{ label }}
        </label>
        <div class="relative group">
            <div
                v-if="$slots.icon"
                class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 transition-colors group-focus-within:text-indigo-500"
            >
                <slot name="icon"></slot>
            </div>
            <input
                :type="type || 'text'"
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                :placeholder="placeholder"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 font-semibold text-slate-800 transition-all duration-300 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                :class="[
                    $slots.icon ? 'pl-11 pr-4' : 'px-4',
                    error ? 'border-rose-500 focus:border-rose-500 focus:ring-rose-500/10' : ''
                ]"
            />
        </div>
        <p v-if="error" class="mt-1 ml-1 text-xs font-bold text-rose-500 animate-in fade-in slide-in-from-top-1 duration-300">
            {{ error }}
        </p>
    </div>
</template>
