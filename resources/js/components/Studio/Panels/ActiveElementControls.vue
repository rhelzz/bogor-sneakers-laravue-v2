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
                    <span class="text-[11px] font-black uppercase tracking-widest text-black">
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
                            <label :for="`logo-outline-${activeElement.id}`" class="text-[11px] font-black uppercase text-sumi tracking-widest cursor-pointer">Outline Logo</label>
                        </div>
                    </div>

                    <div v-if="activeElement.outline.active" class="space-y-3 pt-3 border-t border-gray-50 mt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase text-usuzumi tracking-widest opacity-40">Warna & Ukuran</span>
                            <div class="flex items-center gap-2">
                                <input type="color" v-model="activeElement.outline.color" @input="$emit('updateImageOutline')" class="w-6 h-6 rounded-full border-2 border-white shadow-sm p-0 cursor-pointer overflow-hidden">
                                <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg px-2 py-0.5 shadow-sm focus-within:border-primary transition-colors">
                                    <input type="number" v-model.number="activeElement.outline.size" @input="$emit('updateImageOutline')" min="1" max="15" class="w-8 text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                                    <span class="text-[9px] font-black text-gray-400 select-none">px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Text Specific Controls -->
            <div v-if="activeElement.type === 'text'" class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest ml-1 opacity-40">Gaya & Konten Teks</label>
                    <div class="grid gap-2">
                        <select
                            v-model="activeElement.fontFamily"
                            class="w-full h-10 px-4 bg-white border border-gray-100 rounded-xl text-[11px] font-bold text-black focus:border-primary focus:ring-0 transition-all appearance-none cursor-pointer shadow-sm"
                        >
                            <option v-for="font in FONT_OPTIONS" :key="font.value" :value="font.value">
                                {{ font.label }}
                            </option>
                        </select>
                        <textarea
                            v-model="activeElement.text"
                            class="w-full p-4 bg-white border border-gray-100 rounded-2xl text-[12px] font-bold text-black focus:border-primary focus:ring-0 transition-all resize-none shadow-sm"
                            rows="2"
                            placeholder="Masukkan teks di sini..."
                        ></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest block mb-2 opacity-40">Warna</label>
                        <input type="color" v-model="activeElement.color" class="w-full h-8 p-0 border-none bg-transparent cursor-pointer">
                    </div>
                    <div class="p-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest block mb-2 opacity-40">Stroke (Warna & Ukuran)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" v-model="activeElement.strokeColor" class="w-6 h-6 rounded-full border-2 border-white shadow-sm p-0 cursor-pointer overflow-hidden">
                            <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-0.5 shadow-sm focus-within:border-primary transition-colors flex-grow">
                                <input type="number" v-model.number="activeElement.strokeWidth" min="0" max="15" class="w-full text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                                <span class="text-[9px] font-black text-gray-400 select-none">px</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Positioning & Masking Controls (For both text and image) -->
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest ml-1 opacity-40 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px]">layers</span>
                        Masukkan ke Aksen (Clipping Mask)
                    </label>
                    <select
                        v-model="activeElement.maskId"
                        class="w-full h-10 px-4 bg-white border border-gray-100 rounded-xl text-[11px] font-bold text-black focus:border-primary focus:ring-0 transition-all appearance-none cursor-pointer shadow-sm"
                    >
                        <option :value="null">-- Bebas (Tidak Di-masking) --</option>
                        <option v-for="layer in currentModelMeta?.layers || []" :key="layer.id" :value="layer.id">
                            Aksen {{ layer.id }}
                        </option>
                    </select>
                </div>

                <div v-if="!currentModelMeta?.studio_config" class="space-y-2 bg-indigo/[0.02] p-3 rounded-xl border border-indigo/5">
                    <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest ml-1 opacity-40 flex items-center gap-1 text-red-500">
                        <span class="material-symbols-outlined text-[12px]">build</span>
                        Koreksi Cetak Sisi Sebelahnya
                    </label>
                    <p class="text-[8px] text-gray-400 font-bold mb-2 ml-1">Koreksi geser cetakan elemen untuk sisi sepatu sebelahnya.</p>
                    
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-white border border-gray-200 rounded-lg p-2 shadow-sm focus-within:border-primary transition-colors text-center">
                            <label class="block text-[8px] font-black text-gray-400 mb-1">X (px)</label>
                            <input type="number" v-model.number="activeElement.mx" class="w-full text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-2 shadow-sm focus-within:border-primary transition-colors text-center">
                            <label class="block text-[8px] font-black text-gray-400 mb-1">Y (px)</label>
                            <input type="number" v-model.number="activeElement.my" class="w-full text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-2 shadow-sm focus-within:border-primary transition-colors text-center">
                            <label class="block text-[8px] font-black text-gray-400 mb-1">Sudut (°)</label>
                            <input type="number" v-model.number="activeElement.mrot" class="w-full text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup lang="ts">
import { useStudioStore } from '../../../composables/useStudioStore';

const { activeElement, currentModelMeta } = useStudioStore();

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
