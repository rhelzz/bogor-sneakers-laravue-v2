<template>
    <transition name="fade">
        <div v-if="activeElement" class="mt-4 p-5 rounded-2xl bg-gray-50 border border-gray-100 space-y-4 shadow-xl shadow-black/5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-white shadow-sm flex items-center justify-center">
                        <span class="material-symbols-outlined text-xs text-primary">
                            {{ activeElement.type === 'text' ? 'text_fields' : 'Category' }}
                        </span>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-black">
                        Edit {{ activeElement.type === 'text' ? 'Teks' : 'Logo' }}
                    </span>
                </div>
                <button @click="$emit('remove')" class="w-7 h-7 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all" title="Hapus Elemen">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>

            <!-- Image/Logo Specific Controls -->
            <div v-if="activeElement.type === 'image' && activeElement.outline" class="space-y-3">
                <div class="p-3 bg-white rounded-xl border border-gray-100/50 space-y-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                :id="`logo-outline-${activeElement.id}`"
                                v-model="activeElement.outline.active"
                                @change="$emit('updateImageOutline')"
                                class="w-3.5 h-3.5 rounded border-gray-300 text-black focus:ring-black transition-all"
                            >
                            <label :for="`logo-outline-${activeElement.id}`" class="text-[8px] font-black uppercase text-secondary tracking-widest cursor-pointer">Outline Logo</label>
                        </div>
                    </div>

                    <div v-if="activeElement.outline.active" class="space-y-3 pt-3 border-t border-gray-50 mt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[7px] font-black uppercase text-secondary/40 tracking-widest">Warna & Ukuran</span>
                            <div class="flex items-center gap-2">
                                <input type="color" v-model="activeElement.outline.color" @input="$emit('updateImageOutline')" class="w-6 h-6 rounded-full border-2 border-white shadow-sm p-0 cursor-pointer overflow-hidden">
                                <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg px-2 py-0.5 shadow-sm focus-within:border-primary transition-colors">
                                    <input type="number" v-model.number="activeElement.outline.size" @input="$emit('updateImageOutline')" min="1" max="15" class="w-8 text-[10px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                                    <span class="text-[8px] font-black text-gray-400 select-none">px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Text Specific Controls -->
            <div v-if="activeElement.type === 'text'" class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[8px] font-black uppercase text-secondary/40 tracking-widest ml-1">Gaya & Konten Teks</label>
                    <div class="grid gap-2">
                        <select
                            v-model="activeElement.fontFamily"
                            class="w-full h-10 px-4 bg-white border border-gray-100 rounded-xl text-[10px] font-bold text-black focus:border-primary focus:ring-0 transition-all appearance-none cursor-pointer shadow-sm"
                        >
                            <option v-for="font in FONT_OPTIONS" :key="font.value" :value="font.value">
                                {{ font.label }}
                            </option>
                        </select>
                        <textarea
                            v-model="activeElement.text"
                            class="w-full p-4 bg-white border border-gray-100 rounded-2xl text-xs font-bold text-black focus:border-primary focus:ring-0 transition-all resize-none shadow-sm"
                            rows="2"
                            placeholder="Masukkan teks di sini..."
                        ></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <label class="text-[8px] font-black uppercase text-secondary/40 tracking-widest block mb-2">Warna</label>
                        <input type="color" v-model="activeElement.color" class="w-full h-8 p-0 border-none bg-transparent cursor-pointer">
                    </div>
                    <div class="p-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <label class="text-[8px] font-black uppercase text-secondary/40 tracking-widest block mb-2">Stroke (Warna & Ukuran)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" v-model="activeElement.strokeColor" class="w-6 h-6 rounded-full border-2 border-white shadow-sm p-0 cursor-pointer overflow-hidden">
                            <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-0.5 shadow-sm focus-within:border-primary transition-colors flex-grow">
                                <input type="number" v-model.number="activeElement.strokeWidth" min="0" max="15" class="w-full text-[10px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                                <span class="text-[8px] font-black text-gray-400 select-none">px</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup lang="ts">
import { useStudioStore } from '../../../composables/useStudioStore';

const { activeElement } = useStudioStore();

defineEmits(['remove', 'updateImageOutline']);

const FONT_OPTIONS = [
    { label: 'Modern (Lexend)', value: 'Lexend' },
    { label: 'Clean (Inter)', value: 'Inter' },
    { label: 'Bold (Roboto)', value: 'Roboto' },
    { label: 'Classic (Playfair)', value: 'Playfair Display' },
    { label: 'Sporty (Oswald)', value: 'Oswald' },
    { label: 'Technical (JetBrains)', value: 'JetBrains Mono' },
    { label: 'Retro (Bebas Neue)', value: 'Bebas Neue' }
];
</script>
