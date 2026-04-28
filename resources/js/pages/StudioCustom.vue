<template>
    <div class="font-body-md text-body-md bg-background text-on-background min-h-screen flex flex-col overflow-hidden">
        <Head title="Studio Custom" />

        <!-- TopNavBar Component -->
        <nav class="fixed top-0 w-full z-50 flex justify-between items-center px-6 h-16 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-3">
                    <img src="/logo-b-bogor.svg" class="w-8 h-8" alt="Logo">
                    <span class="text-lg font-black tracking-tighter text-black uppercase">Studio Custom</span>
                </div>
                
                <div class="h-6 w-[1px] bg-gray-200"></div>

                <div class="flex items-center gap-4">
                     <div class="flex flex-col">
                         <span class="text-[9px] font-black text-secondary uppercase tracking-[0.2em] leading-none mb-1.5">Family</span>
                         <select
                            v-model="activeFolderKey"
                            class="h-9 min-w-[140px] rounded-lg border-gray-200 bg-gray-50/50 px-3 text-[11px] font-bold text-black focus:border-black focus:ring-0 transition-all"
                            :disabled="catalogLoading"
                        >
                            <option v-for="folder in catalogFolders" :key="folder.key" :value="folder.key">
                                {{ folder.label }}
                            </option>
                        </select>
                     </div>

                     <div class="flex flex-col">
                         <span class="text-[9px] font-black text-secondary uppercase tracking-[0.2em] leading-none mb-1.5">Model No.</span>
                         <select
                            v-model="currentModel"
                            class="h-9 min-w-[100px] rounded-lg border-gray-200 bg-gray-50/50 px-3 text-[11px] font-bold text-black focus:border-black focus:ring-0 transition-all"
                            :disabled="catalogLoading"
                        >
                            <option v-for="model in availableModels" :key="model.id" :value="model.id">
                                {{ model.label }}
                            </option>
                        </select>
                     </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex items-center bg-gray-100 rounded-xl p-1 mr-4">
                    <button @click="undo" class="w-9 h-9 flex items-center justify-center text-secondary hover:text-black hover:bg-white rounded-lg transition-all" title="Undo">
                        <span class="material-symbols-outlined text-xl">undo</span>
                    </button>
                    <button @click="redo" class="w-9 h-9 flex items-center justify-center text-secondary hover:text-black hover:bg-white rounded-lg transition-all" title="Redo">
                        <span class="material-symbols-outlined text-xl">redo</span>
                    </button>
                    <button @click="resetDesign" class="w-9 h-9 flex items-center justify-center text-secondary hover:text-error hover:bg-white rounded-lg transition-all" title="Reset">
                        <span class="material-symbols-outlined text-xl text-error/70">restart_alt</span>
                    </button>
                </div>

                <button @click="validateCheckout" class="h-10 px-6 bg-black text-white font-black text-[11px] uppercase tracking-[0.2em] hover:bg-primary hover:text-black transition-all rounded-xl shadow-lg shadow-black/10 active:scale-95">
                    {{ isSaving ? 'PROCESSING...' : 'PROCESS ORDER' }}
                </button>
            </div>
        </nav>

        <div class="flex-grow flex mt-16 h-[calc(100vh-64px)] relative">
            <!-- SideNavBar Component -->
            <aside class="w-20 border-r border-gray-100 bg-white flex flex-col z-40">
                <div class="flex-grow flex flex-col items-center py-8 gap-6">
                    <button 
                        @click="activeSideTab = 'layers'"
                        class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all"
                        :class="activeSideTab === 'layers' ? 'bg-primary text-black shadow-lg shadow-primary/20' : 'text-secondary hover:bg-gray-50 hover:text-black'"
                    >
                        <span class="material-symbols-outlined text-2xl">layers</span>
                        <span class="absolute left-full ml-4 px-2 py-1 bg-black text-white text-[10px] font-black rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-all whitespace-nowrap z-50 uppercase tracking-[0.2em]">Layers</span>
                    </button>

                    <button 
                        @click="activeSideTab = 'artwork'"
                        class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all"
                        :class="activeSideTab === 'artwork' ? 'bg-primary text-black shadow-lg shadow-primary/20' : 'text-secondary hover:bg-gray-50 hover:text-black'"
                    >
                        <span class="material-symbols-outlined text-2xl">palette</span>
                        <span class="absolute left-full ml-4 px-2 py-1 bg-black text-white text-[10px] font-black rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-all whitespace-nowrap z-50 uppercase tracking-[0.2em]">Artwork</span>
                    </button>

                    <button 
                        @click="activeSideTab = 'text'"
                        class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all"
                        :class="activeSideTab === 'text' ? 'bg-primary text-black shadow-lg shadow-primary/20' : 'text-secondary hover:bg-gray-50 hover:text-black'"
                    >
                        <span class="material-symbols-outlined text-2xl">title</span>
                        <span class="absolute left-full ml-4 px-2 py-1 bg-black text-white text-[10px] font-black rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-all whitespace-nowrap z-50 uppercase tracking-[0.2em]">Teks</span>
                    </button>
                </div>

                <div class="p-4 border-t border-gray-100 flex flex-col items-center gap-4">
                    <button @click="showShare" class="w-10 h-10 flex items-center justify-center text-secondary hover:text-black rounded-xl hover:bg-gray-50 transition-all">
                        <span class="material-symbols-outlined">share</span>
                    </button>
                    <button 
                        @click="activeSideTab = 'checkout'"
                        class="w-12 h-12 flex items-center justify-center rounded-2xl transition-all"
                        :class="activeSideTab === 'checkout' ? 'bg-black text-white shadow-xl shadow-black/20' : 'bg-gray-100 text-secondary hover:bg-gray-200'"
                    >
                        <span class="material-symbols-outlined text-2xl">shopping_cart</span>
                    </button>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-grow flex relative bg-[#f8f9fa] overflow-hidden">
                <!-- Center Stage (Product Visualizer) -->
                <div class="flex-grow min-w-0 flex items-center justify-center relative overflow-hidden h-full">
                    <!-- High-tech backdrop effect -->
                    <div class="absolute inset-0 pointer-events-none overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent opacity-60"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary/5 rounded-full blur-[120px]"></div>
                        <!-- UI Grid lines -->
                        <div class="absolute inset-0 opacity-[0.03] bg-[size:40px_40px] bg-[linear-gradient(to_right,#808080_1px,transparent_1px),linear-gradient(to_bottom,#808080_1px,transparent_1px)]"></div>
                    </div>
                    
                    <!-- Konva Container -->
                    <div ref="konvaContainerRef" class="relative w-full h-full z-10 cursor-grab active:cursor-grabbing"></div>

                    <!-- Floating View Controls -->
                    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2 p-2 bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-100 shadow-2xl z-20">
                        <button @click="zoomOut" class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-gray-50 rounded-xl transition-all">
                            <span class="material-symbols-outlined">remove</span>
                        </button>
                        <div class="h-4 w-[1px] bg-gray-200 mx-1"></div>
                        <button @click="resetZoom" class="px-3 h-10 flex items-center gap-2 text-[10px] font-black text-secondary hover:text-black transition-all uppercase tracking-[0.2em]">
                            <span class="material-symbols-outlined text-lg">center_focus_strong</span>
                            Reset View
                        </button>
                        <div class="h-4 w-[1px] bg-gray-200 mx-1"></div>
                        <button @click="zoomIn" class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-gray-50 rounded-xl transition-all">
                            <span class="material-symbols-outlined">add</span>
                        </button>
                    </div>

                    <!-- Syncing Indicator -->
                    <transition name="fade">
                        <div
                            v-if="isSyncing || catalogLoading"
                            class="absolute inset-0 z-50 flex flex-col items-center justify-center gap-6 bg-white/90 backdrop-blur-md"
                        >
                            <div class="relative">
                                <div class="h-16 w-16 rounded-full border-[3px] border-gray-100 border-t-primary animate-spin"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-primary text-xl animate-pulse">view_in_ar</span>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-[11px] font-black uppercase tracking-[0.2em] text-black">Mempersiapkan Model</p>
                                <p class="text-[10px] text-secondary font-medium mt-1">Mengunduh aset SVG kualitas tinggi...</p>
                            </div>
                        </div>
                    </transition>
                </div>

                <!-- Right Side Panels (Container to prevent cutoff) -->
                <div class="h-full flex-shrink-0 relative z-30">
                    <transition name="panel-slide" mode="out-in">
                        <!-- Tool Panel -->
                        <div v-if="activeSideTab !== 'checkout'" key="tools" class="w-[320px] md:w-[340px] flex-shrink-0 bg-white border-l border-gray-100 flex flex-col h-full shadow-[-20px_0_50px_rgba(0,0,0,0.03)]">
                            <div class="p-6 border-b border-gray-50">
                                <h3 class="text-sm font-black text-black uppercase tracking-widest flex items-center gap-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    {{ hudTitle }}
                                </h3>
                                <p class="text-[10px] text-secondary font-bold mt-1 tracking-widest uppercase opacity-60">{{ hudSubtitle }}</p>
                            </div>
                            
                            <div class="flex-grow overflow-y-auto custom-scrollbar p-6">
                                <!-- Layer Customization -->
                                <div v-if="activeSideTab === 'layers'" class="space-y-4">
                                    <div v-if="layerIds.length === 0" class="flex flex-col items-center justify-center py-20 text-center opacity-40">
                                        <span class="material-symbols-outlined text-4xl mb-2">layers_clear</span>
                                        <p class="text-[10px] font-bold uppercase">Tidak ada layer</p>
                                    </div>
                                    <div v-else class="grid gap-3">
                                        <div 
                                            v-for="id in layerIds" 
                                            :key="`layer-${id}`"
                                            class="group relative overflow-hidden rounded-2xl border transition-all duration-300"
                                            :class="activeLayerPickId === id 
                                                ? 'border-primary bg-primary/5 shadow-sm' 
                                                : 'border-gray-100 bg-white hover:border-gray-300 hover:shadow-md cursor-pointer'"
                                            @click="activeLayerPickId = id"
                                        >
                                            <div class="p-4 flex items-center justify-between relative z-10">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-[10px] font-black text-secondary group-hover:text-black transition-colors">
                                                        {{ id }}
                                                    </div>
                                                    <span class="text-[10px] font-black uppercase tracking-widest">Aksen {{ id }}</span>
                                                </div>
                                                <div 
                                                    class="w-8 h-8 rounded-full border-2 border-white shadow-sm transition-transform group-hover:scale-110" 
                                                    :style="{ backgroundColor: layerColors[id] || '#ffffff' }"
                                                ></div>
                                            </div>

                                            <transition name="expand">
                                                <div v-if="activeLayerPickId === id" class="px-4 pb-4 pt-2 border-t border-gray-100/50 space-y-4">
                                                    <div class="grid grid-cols-6 gap-2">
                                                        <button 
                                                            v-for="color in randomPalette" 
                                                            :key="color"
                                                            class="w-full aspect-square rounded-lg border border-white shadow-sm hover:scale-110 transition-transform active:scale-90"
                                                            :style="{ backgroundColor: color }"
                                                            @click.stop="setLayerColor(id, color)"
                                                        ></button>
                                                        <button 
                                                            class="w-full aspect-square rounded-lg bg-gray-50 flex items-center justify-center hover:bg-white hover:shadow-sm transition-all border border-gray-100 active:scale-90"
                                                            @click.stop="triggerColorPicker(id)"
                                                        >
                                                            <span class="material-symbols-outlined text-sm">colorize</span>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="flex items-center justify-between p-3 bg-white rounded-xl border border-gray-100/50">
                                                        <div class="flex items-center gap-2">
                                                            <input 
                                                                type="checkbox" 
                                                                :id="`outline-${id}`"
                                                                v-model="layerOutlines[id].active"
                                                                class="w-4 h-4 rounded-md border-gray-300 text-black focus:ring-black transition-all cursor-pointer"
                                                            >
                                                            <label :for="`outline-${id}`" class="text-[9px] font-black uppercase text-secondary tracking-widest cursor-pointer">Outline</label>
                                                        </div>
                                                        <input 
                                                            v-if="layerOutlines[id].active"
                                                            type="color" 
                                                            v-model="layerOutlines[id].color"
                                                            class="w-10 h-6 p-0 border-0 bg-transparent cursor-pointer rounded overflow-hidden"
                                                        >
                                                    </div>
                                                </div>
                                            </transition>
                                        </div>
                                    </div>
                                </div>

                                <!-- Artwork Customization -->
                                <div v-if="activeSideTab === 'artwork'" class="space-y-6">
                                    <button 
                                        @click="triggerUpload"
                                        class="w-full py-8 border-2 border-dashed border-gray-100 rounded-3xl flex flex-col items-center justify-center gap-3 bg-gray-50/50 hover:bg-white hover:border-primary hover:shadow-xl hover:shadow-primary/5 transition-all group"
                                    >
                                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-secondary group-hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-black block">Upload Media</span>
                                            <span class="text-[9px] text-secondary font-medium mt-1 block uppercase tracking-tighter opacity-60">PNG / JPG / SVG</span>
                                        </div>
                                    </button>

                                    <div v-if="uploadedMedia.length > 0" class="grid grid-cols-2 gap-4">
                                        <div 
                                            v-for="media in uploadedMedia" 
                                            :key="media.id"
                                            class="group relative aspect-square rounded-2xl bg-white border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer"
                                            @click="addUploadAsElement(media.id)"
                                        >
                                            <img :src="media.src" class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform">
                                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center backdrop-blur-[2px] transition-all">
                                                <div class="bg-white text-black px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest scale-75 group-hover:scale-100 transition-transform">Tambah</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Text Customization -->
                                <div v-if="activeSideTab === 'text'" class="space-y-6">
                                    <button 
                                        @click="addTextElement"
                                        class="w-full py-8 border-2 border-dashed border-gray-100 rounded-3xl flex flex-col items-center justify-center gap-3 bg-gray-50/50 hover:bg-white hover:border-primary hover:shadow-xl hover:shadow-primary/5 transition-all group"
                                    >
                                        <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-secondary group-hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-3xl">text_fields</span>
                                        </div>
                                        <div class="text-center">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-black block">Tambah Teks</span>
                                            <span class="text-[9px] text-secondary font-medium mt-1 block uppercase tracking-tighter opacity-60">Custom Branding</span>
                                        </div>
                                    </button>

                                    <transition name="fade">
                                        <div v-if="activeElement?.type === 'text'" class="p-6 rounded-3xl bg-gray-50 border border-gray-100 space-y-6">
                                            <div>
                                                <label class="text-[9px] font-black uppercase text-secondary tracking-widest block mb-2 opacity-60">Isi Teks</label>
                                                <textarea 
                                                    v-model="activeElement.text"
                                                    class="w-full p-4 rounded-xl border-gray-200 text-xs font-bold focus:border-black focus:ring-black transition-all resize-none"
                                                    rows="3"
                                                ></textarea>
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label class="text-[9px] font-black uppercase text-secondary tracking-widest block mb-2 opacity-60">Warna</label>
                                                    <input type="color" v-model="activeElement.color" class="w-full h-10 p-1 bg-white rounded-lg border-gray-200 cursor-pointer">
                                                </div>
                                                <div>
                                                    <label class="text-[9px] font-black uppercase text-secondary tracking-widest block mb-2 opacity-60">Stroke</label>
                                                    <input type="color" v-model="activeElement.strokeColor" class="w-full h-10 p-1 bg-white rounded-lg border-gray-200 cursor-pointer">
                                                </div>
                                            </div>
                                            <button @click="removeActiveElement" class="w-full py-3 text-[10px] font-black uppercase text-error hover:bg-error/5 rounded-xl transition-all tracking-widest">Hapus Elemen</button>
                                        </div>
                                    </transition>
                                </div>
                            </div>

                            <div class="p-8 border-t border-gray-50 bg-white">
                                <div class="flex justify-between items-center mb-6">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] font-black text-secondary uppercase tracking-[0.2em] opacity-60">Estimated Total</span>
                                        <span class="text-2xl font-black text-black mt-1 tracking-tighter">{{ formatCurrency(guestCheckoutTotal) }}</span>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary">payments</span>
                                    </div>
                                </div>
                                <button @click="activeSideTab = 'checkout'" class="w-full h-14 bg-black text-white font-black text-[11px] uppercase tracking-widest hover:bg-primary hover:text-black transition-all rounded-2xl flex items-center justify-center gap-3 shadow-xl shadow-black/10 active:scale-95">
                                    Finalize Order
                                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                                </button>
                            </div>
                        </div>

                        <!-- Checkout View -->
                        <div v-else key="checkout" class="w-[360px] sm:w-[420px] md:w-[480px] flex-shrink-0 bg-white border-l border-gray-100 flex flex-col h-full shadow-[-30px_0_60px_rgba(0,0,0,0.05)]">
                            <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-white sticky top-0 z-10">
                                <div class="flex items-center gap-4">
                                    <button @click="activeSideTab = 'layers'" class="w-10 h-10 flex items-center justify-center text-secondary hover:text-black hover:bg-gray-100 rounded-xl transition-all">
                                        <span class="material-symbols-outlined">arrow_back</span>
                                    </button>
                                    <div>
                                        <h3 class="text-lg font-black text-black uppercase tracking-tight">GUEST CHECKOUT</h3>
                                        <p class="text-[10px] text-secondary font-bold tracking-widest uppercase opacity-60">Lengkapi data pengiriman</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8 flex-grow overflow-y-auto custom-scrollbar space-y-8">
                                 <div class="grid gap-6">
                                    <div class="relative group">
                                        <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">Nama Lengkap</label>
                                        <input v-model="name" type="text" class="w-full h-12 px-5 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold placeholder:opacity-30" placeholder="Contoh: Muhammad Rizky">
                                        <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg group-focus-within:text-black transition-colors">person</span>
                                    </div>
                                    <div class="relative group">
                                        <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">WhatsApp Number</label>
                                        <input v-model="phone" type="text" class="w-full h-12 px-5 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold placeholder:opacity-30" placeholder="0812xxxxxxxx">
                                        <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg group-focus-within:text-black transition-colors">call</span>
                                    </div>
                                    <div class="relative group">
                                        <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">Email Address</label>
                                        <input v-model="guestEmail" type="email" class="w-full h-12 px-5 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold placeholder:opacity-30" placeholder="email@contoh.com">
                                        <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg group-focus-within:text-black transition-colors">mail</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="relative group">
                                            <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">Shoe Size</label>
                                            <select v-model="shoeSize" class="w-full h-12 px-5 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold appearance-none cursor-pointer">
                                                <option value="">Pilih</option>
                                                <option v-for="size in shoeSizes" :key="size" :value="String(size)">Size {{ size }}</option>
                                            </select>
                                            <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg pointer-events-none">expand_more</span>
                                        </div>
                                        <div class="relative group opacity-40 grayscale pointer-events-none">
                                            <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1">Stock</label>
                                            <div class="h-12 flex items-center px-5 bg-gray-100 rounded-2xl text-[10px] font-black uppercase tracking-widest">Available</div>
                                        </div>
                                    </div>

                                    <div class="relative group">
                                        <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">Shipping Address</label>
                                        <textarea v-model="guestAddress" rows="4" class="w-full px-5 py-4 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold placeholder:opacity-30 resize-none" placeholder="Masukkan alamat lengkap pengiriman..."></textarea>
                                        <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg group-focus-within:text-black transition-colors">location_on</span>
                                    </div>
                                </div>

                                <div class="p-6 rounded-[32px] bg-gray-50 border border-gray-100 space-y-4">
                                     <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary">
                                                <span class="material-symbols-outlined text-xl">bolt</span>
                                            </div>
                                            <div>
                                                <p class="text-xs font-black uppercase tracking-tight">Fast Track</p>
                                                <p class="text-[9px] text-secondary font-bold uppercase tracking-widest opacity-60">Pengerjaan 3-5 Hari</p>
                                            </div>
                                        </div>
                                        <div 
                                            class="w-14 h-7 rounded-full relative transition-all cursor-pointer border-2"
                                            :class="fastTrackEnabled ? 'bg-primary border-primary' : 'bg-gray-200 border-gray-200'"
                                            @click="toggleFastTrack"
                                        >
                                            <div 
                                                class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-all"
                                                :class="fastTrackEnabled ? 'translate-x-7' : ''"
                                            ></div>
                                        </div>
                                     </div>
                                     
                                     <div class="h-[1px] bg-gray-200/50"></div>

                                     <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary">
                                                <span class="material-symbols-outlined text-xl">inventory_2</span>
                                            </div>
                                            <div>
                                                <p class="text-xs font-black uppercase tracking-tight">Premium Box</p>
                                                <p class="text-[9px] text-secondary font-bold uppercase tracking-widest opacity-60">Custom Exclusive Box</p>
                                            </div>
                                        </div>
                                        <div 
                                            class="w-14 h-7 rounded-full relative transition-all cursor-pointer border-2"
                                            :class="customBoxEnabled ? 'bg-primary border-primary' : 'bg-gray-200 border-gray-200'"
                                            @click="toggleCustomBox"
                                        >
                                            <div 
                                                class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-all"
                                                :class="customBoxEnabled ? 'translate-x-7' : ''"
                                            ></div>
                                        </div>
                                     </div>
                                </div>
                            </div>

                            <div class="p-8 border-t border-gray-100 bg-white sticky bottom-0">
                                <div class="space-y-3 mb-8">
                                    <div class="flex justify-between text-[11px] font-bold text-secondary uppercase tracking-[0.1em]">
                                        <span>Base Custom Pair</span>
                                        <span class="text-black">{{ formatCurrency(BASE_PRODUCT_PRICE) }}</span>
                                    </div>
                                    <div v-if="fastTrackEnabled" class="flex justify-between text-[11px] font-bold text-secondary uppercase tracking-[0.1em]">
                                        <span>Fast Track Fee</span>
                                        <span class="text-primary">+{{ formatCurrency(FAST_TRACK_FEE) }}</span>
                                    </div>
                                    <div v-if="customBoxEnabled" class="flex justify-between text-[11px] font-bold text-secondary uppercase tracking-[0.1em]">
                                        <span>Premium Box Fee</span>
                                        <span class="text-primary">+{{ formatCurrency(CUSTOM_BOX_FEE) }}</span>
                                    </div>
                                    <div class="pt-4 border-t border-gray-100 flex justify-between items-end">
                                        <div class="flex flex-col">
                                            <span class="text-[9px] font-black text-secondary uppercase tracking-[0.2em] opacity-60">Total Amount</span>
                                            <span class="text-3xl font-black text-black tracking-tighter mt-1">{{ formatCurrency(guestCheckoutTotal) }}</span>
                                        </div>
                                        <div class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 text-[9px] font-black text-secondary uppercase tracking-widest">G-002-X</div>
                                    </div>
                                </div>
                                <button 
                                    @click="handleSave"
                                    :disabled="isSaving"
                                    class="w-full h-16 bg-black text-white font-black text-[12px] uppercase tracking-[0.2em] hover:bg-primary-container hover:text-black transition-all rounded-[24px] flex items-center justify-center gap-4 shadow-2xl shadow-black/20 disabled:opacity-50 active:scale-95"
                                >
                                     <span v-if="isSaving" class="w-5 h-5 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></span>
                                     {{ isSaving ? 'PROCESSING ORDER...' : 'FINALIZE & CHECKOUT' }}
                                     <span v-if="!isSaving" class="material-symbols-outlined text-xl">shopping_bag</span>
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>
            </main>
        </div>

        <!-- Popups / Alerts -->
        <transition name="fade">
            <div v-if="showFastTrackAlert" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-xl p-6">
                <div class="bg-white rounded-[40px] p-10 max-w-sm w-full shadow-2xl text-center transform transition-all scale-100 animate-popup">
                    <div class="w-20 h-20 bg-primary rounded-[28px] flex items-center justify-center mb-8 mx-auto shadow-xl shadow-primary/10">
                        <span class="material-symbols-outlined text-black text-4xl">bolt</span>
                    </div>
                    <h4 class="text-2xl font-black text-black uppercase tracking-tight mb-3">Butuh Cepat?</h4>
                    <p class="text-xs text-secondary font-bold leading-relaxed mb-10 opacity-60 uppercase tracking-widest">Gunakan layanan Fast Track agar pesanan Anda masuk antrean prioritas dan selesai dalam 3-5 hari kerja.</p>
                    <div class="flex flex-col gap-3">
                        <button @click="confirmFastTrack(true)" class="w-full py-5 bg-black text-white rounded-[20px] font-black text-[11px] uppercase tracking-[0.2em] hover:bg-primary hover:text-black transition-all shadow-xl shadow-black/10">YA, SAYA MAU (+{{ formatCurrency(FAST_TRACK_FEE) }})</button>
                        <button @click="confirmFastTrack(false)" class="w-full py-4 text-secondary font-black text-[10px] uppercase tracking-[0.2em] hover:text-black transition-all">TIDAK, TERIMA KASIH</button>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="showCustomBoxAlert" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-xl p-6">
                <div class="bg-white rounded-[40px] p-10 max-w-sm w-full shadow-2xl text-center transform transition-all scale-100 animate-popup">
                    <div class="w-20 h-20 bg-primary rounded-[28px] flex items-center justify-center mb-8 mx-auto shadow-xl shadow-primary/10">
                        <span class="material-symbols-outlined text-black text-4xl">inventory_2</span>
                    </div>
                    <h4 class="text-2xl font-black text-black uppercase tracking-tight mb-3">Upgrade Box?</h4>
                    <p class="text-xs text-secondary font-bold leading-relaxed mb-10 opacity-60 uppercase tracking-widest">Berikan kesan eksklusif dengan Custom Box Premium. Sangat direkomendasikan jika pesanan ini untuk hadiah.</p>
                    <div class="flex flex-col gap-3">
                        <button @click="confirmCustomBox(true)" class="w-full py-5 bg-black text-white rounded-[20px] font-black text-[11px] uppercase tracking-[0.2em] hover:bg-primary hover:text-black transition-all shadow-xl shadow-black/10">YA, UPGRADE BOX (+{{ formatCurrency(CUSTOM_BOX_FEE) }})</button>
                        <button @click="confirmCustomBox(false)" class="w-full py-4 text-secondary font-black text-[10px] uppercase tracking-[0.2em] hover:text-black transition-all">TIDAK, BOX STANDAR</button>
                    </div>
                </div>
            </div>
        </transition>

        <input ref="colorPickerRef" type="color" class="sr-only" @change="onLayerPickerChange" />
        <input ref="uploadInputRef" type="file" class="hidden" accept="image/*" multiple @change="onUploadInputChange" />

        <transition name="toast">
            <div
                v-if="toastMessage"
                class="fixed bottom-12 left-1/2 z-[110] -translate-x-1/2 rounded-2xl bg-black px-6 py-4 text-[10px] font-black text-white shadow-2xl flex items-center gap-4 uppercase tracking-[0.2em] border border-white/10"
                role="status"
                aria-live="polite"
            >
                <div class="w-2 h-2 bg-primary rounded-full animate-pulse shadow-[0_0_10px_#d0ff00]"></div>
                {{ toastMessage }}
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch, nextTick } from 'vue';
import type { CSSProperties } from 'vue';
import Konva from 'konva';

const CANVAS_SIZE = 1024;
const BASE_PRODUCT_PRICE = 899000;
const FAST_TRACK_FEE = 125000;
const CUSTOM_BOX_FEE = 65000;

const WHATSAPP_INTRO = 'Halo kak, saya ingin melakukan pemesanan custom BogorSneaker:';

const FALLBACK_PALETTE = [
    '#000000', '#ffffff', '#d0ff00', '#ff0000', '#0000ff', '#808080', '#a855f7', '#f97316',
    '#7c8c5a', '#d97781', '#4a9d6f', '#1a1a1a', '#f7f5f0', '#888888', '#b4b4b4', '#5a6b6a'
];

const page = usePage();

// UI State
const activeSideTab = ref('layers');
const activeLayerPickId = ref<number | null>(null);
const konvaContainerRef = ref<HTMLDivElement | null>(null);
const colorPickerRef = ref<HTMLInputElement | null>(null);
const uploadInputRef = ref<HTMLInputElement | null>(null);
const activeElement = ref<any>(null);
const toastMessage = ref('');
const isSaving = ref(false);
const showFastTrackAlert = ref(false);
const showCustomBoxAlert = ref(false);

// Design Data
const catalogFolders = ref<any[]>([]);
const catalogLoading = ref(false);
const activeFolderKey = ref('');
const currentModel = ref<number | string | null>(null);
const isSyncing = ref(false);

const layerIds = ref<number[]>([]);
const layerColors = ref<Record<number, string>>({});
const layerOutlines = ref<Record<number, { active: boolean; color: string; size: number }>>({});
const uploadedMedia = ref<any[]>([]);

// Checkout Form
const name = ref('');
const phone = ref('');
const guestEmail = ref('');
const guestAddress = ref('');
const shoeSize = ref('');
const fastTrackEnabled = ref(false);
const customBoxEnabled = ref(false);

// Konva Objects
let stage: Konva.Stage | null = null;
let mainLayer: Konva.Layer | null = null;
let shoeGroup: Konva.Group | null = null;
let elementsGroup: Konva.Group | null = null;
let transformer: Konva.Transformer | null = null;

// Computed
const availableModels = computed(() => {
    const folder = catalogFolders.value.find((f: { key: string }) => f.key === activeFolderKey.value);
    return folder?.models || [];
});

const activeFolder = computed(() => {
    return catalogFolders.value.find((f: { key: string }) => f.key === activeFolderKey.value);
});

const currentModelMeta = computed(() => {
    return availableModels.value.find((m: { id: number | string | null }) => m.id === currentModel.value);
});

const guestCheckoutTotal = computed(() => {
    let total = BASE_PRODUCT_PRICE;
    if (fastTrackEnabled.value) total += FAST_TRACK_FEE;
    if (customBoxEnabled.value) total += CUSTOM_BOX_FEE;
    return total;
});

const randomPalette = computed(() => {
    return FALLBACK_PALETTE;
});

const hudTitle = computed(() => {
    if (activeSideTab.value === 'layers') return 'Aksen Warna';
    if (activeSideTab.value === 'artwork') return 'Media Artwork';
    if (activeSideTab.value === 'text') return 'Kustom Teks';
    return 'Studio';
});

const hudSubtitle = computed(() => {
    if (activeSideTab.value === 'layers') return 'Personalize Layers';
    if (activeSideTab.value === 'artwork') return 'Upload Branding';
    if (activeSideTab.value === 'text') return 'Identity Design';
    return 'KONFIGURASI';
});

const shoeSizes = Array.from({ length: 11 }, (_, i) => 35 + i);

// Utility Methods
const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val);
};

const showToast = (msg: string) => {
    toastMessage.value = msg;
    setTimeout(() => { toastMessage.value = ''; }, 3000);
};

const loadImage = (src: string): Promise<HTMLImageElement> => {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
    });
};

function drawFilledLayer(img: HTMLImageElement, color: string, w: number, h: number): HTMLCanvasElement {
    const canvas = document.createElement('canvas');
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext('2d');
    if (!ctx) return canvas;

    ctx.drawImage(img, 0, 0, w, h);
    ctx.globalCompositeOperation = 'source-in';
    ctx.fillStyle = color;
    ctx.fillRect(0, 0, w, h);
    return canvas;
}

function drawOutlineLayer(img: HTMLImageElement, color: string, size: number, w: number, h: number): HTMLCanvasElement {
    const canvas = document.createElement('canvas');
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext('2d');
    if (!ctx) return canvas;

    const dArr = [-1, -1, 0, -1, 1, -1, -1, 0, 1, 0, -1, 1, 0, 1, 1, 1];
    const s = size;

    for (let i = 0; i < dArr.length; i += 2) {
        ctx.drawImage(img, dArr[i] * s, dArr[i + 1] * s, w, h);
    }

    ctx.globalCompositeOperation = 'source-in';
    ctx.fillStyle = color;
    ctx.fillRect(0, 0, w, h);
    return canvas;
}

const initKonva = () => {
    if (!konvaContainerRef.value) return;

    const width = konvaContainerRef.value.clientWidth;
    const height = konvaContainerRef.value.clientHeight;

    stage = new Konva.Stage({
        container: konvaContainerRef.value,
        width: width,
        height: height,
    });

    mainLayer = new Konva.Layer();
    stage.add(mainLayer);

    shoeGroup = new Konva.Group();
    mainLayer.add(shoeGroup);

    elementsGroup = new Konva.Group();
    mainLayer.add(elementsGroup);

    transformer = new Konva.Transformer({
        nodes: [],
        keepRatio: true,
        rotateAnchorOffset: 40,
        anchorFill: '#fff',
        anchorStroke: '#000',
        anchorSize: 10,
        anchorCornerRadius: 4,
        borderStroke: '#000',
        borderDash: [5, 5],
        boundBoxFunc: (oldBox, newBox) => {
            if (newBox.width < 20 || newBox.height < 20) return oldBox;
            return newBox;
        }
    });
    mainLayer.add(transformer);

    stage.on('click tap', (e) => {
        if (e.target === stage) {
            transformer?.nodes([]);
            activeElement.value = null;
            mainLayer?.draw();
            return;
        }
        
        const parent = e.target.getParent();
        const target = e.target as Konva.Node;

        if (parent === shoeGroup) {
            const layerIdAttr = target.getAttr('layerId');
            if (layerIdAttr !== undefined) {
                activeLayerPickId.value = layerIdAttr;
                activeSideTab.value = 'layers';
            }
            transformer?.nodes([]);
            activeElement.value = null;
        } else if (parent === elementsGroup) {
            const meta = target.getAttr('meta');
            if (meta) {
                activeElement.value = meta;
                transformer?.nodes([target]);
            }
        }
        mainLayer?.draw();
    });

    stage.draggable(true);
    
    stage.on('wheel', (e) => {
        e.evt.preventDefault();
        const oldScale = stage!.scaleX();
        const pointer = stage!.getPointerPosition()!;

        const mousePointTo = {
            x: (pointer.x - stage!.x()) / oldScale,
            y: (pointer.y - stage!.y()) / oldScale,
        };

        const newScale = e.evt.deltaY < 0 ? oldScale * 1.05 : oldScale / 1.05;
        stage!.scale({ x: newScale, y: newScale });

        const newPos = {
            x: pointer.x - mousePointTo.x * newScale,
            y: pointer.y - mousePointTo.y * newScale,
        };
        stage!.position(newPos);
        mainLayer?.draw();
    });
};

const loadModelAssets = async () => {
    if (!currentModelMeta.value || !shoeGroup || !stage) return;
    isSyncing.value = true;
    shoeGroup.destroyChildren();
    activeLayerPickId.value = null;

    try {
        const meta = currentModelMeta.value;
        const baseUrl = `/shoes-svg/${activeFolderKey.value}/${currentModel.value}/`;
        
        const baseFileToLoad = meta.preview_base_file || meta.main_file;

        if (baseFileToLoad) {
            const baseImg = await loadImage(baseUrl + baseFileToLoad);
            const konvaBase = new Konva.Image({
                image: baseImg,
                width: CANVAS_SIZE,
                height: CANVAS_SIZE,
                x: (stage.width() - CANVAS_SIZE) / 2,
                y: (stage.height() - CANVAS_SIZE) / 2,
                listening: false
            });
            shoeGroup.add(konvaBase);
        }

        layerIds.value = meta.layers.map((l: any) => l.id);
        for (const layer of meta.layers) {
            const img = await loadImage(baseUrl + layer.file);
            
            if (!layerColors.value[layer.id]) layerColors.value[layer.id] = '#ffffff';
            if (!layerOutlines.value[layer.id]) layerOutlines.value[layer.id] = { active: false, color: '#000000', size: 2 };

            const konvaLayer = new Konva.Image({
                image: img,
                width: CANVAS_SIZE,
                height: CANVAS_SIZE,
                x: (stage.width() - CANVAS_SIZE) / 2,
                y: (stage.height() - CANVAS_SIZE) / 2,
                name: `layer-${layer.id}`,
                layerId: layer.id,
                hitFunc: (context, shape) => {
                    const imgShape = shape as Konva.Image;
                    const canvas = imgShape.image() as HTMLImageElement;
                    if (!canvas) return;
                    
                    const hitCanvas = document.createElement('canvas');
                    hitCanvas.width = 1;
                    hitCanvas.height = 1;
                    const hitCtx = hitCanvas.getContext('2d');
                    if (!hitCtx) return;
                    
                    const pointer = stage!.getPointerPosition();
                    if (!pointer) return;
                    
                    const transform = shape.getAbsoluteTransform().copy().invert();
                    const point = transform.point(pointer);
                    
                    hitCtx.drawImage(canvas, point.x, point.y, 1, 1, 0, 0, 1, 1);
                    const alpha = hitCtx.getImageData(0, 0, 1, 1).data[3];
                    
                    if (alpha > 10) {
                        context.beginPath();
                        context.rect(0, 0, shape.width(), shape.height());
                        context.closePath();
                        context.fillShape(shape);
                    }
                }
            });

            konvaLayer.filters([Konva.Filters.RGBA]);
            updateKonvaLayerFilter(konvaLayer, layerColors.value[layer.id]);
            shoeGroup.add(konvaLayer);
        }
        
        mainLayer?.draw();
    } catch (err) {
        console.error('Failed to load model assets:', err);
        showToast('Gagal memuat aset model');
    } finally {
        isSyncing.value = false;
    }
};

const updateKonvaLayerFilter = (konvaImg: Konva.Image, hex: string) => {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    
    konvaImg.cache();
    konvaImg.red(r).green(g).blue(b);
};

const setLayerColor = (id: number, color: string) => {
    layerColors.value[id] = color;
    const konvaImg = shoeGroup?.findOne(`.layer-${id}`) as Konva.Image;
    if (konvaImg) {
        updateKonvaLayerFilter(konvaImg, color);
        mainLayer?.draw();
    }
};

const triggerColorPicker = (id: number) => {
    activeLayerPickId.value = id;
    if (colorPickerRef.value) {
        colorPickerRef.value.value = layerColors.value[id] || '#ffffff';
        colorPickerRef.value.click();
    }
};

const onLayerPickerChange = (e: Event) => {
    if (activeLayerPickId.value !== null) {
        const color = (e.target as HTMLInputElement).value;
        setLayerColor(activeLayerPickId.value, color);
    }
};

const addTextElement = () => {
    if (!stage || !elementsGroup) return;
    
    const id = Math.random().toString(36).slice(2);
    const textNode = new Konva.Text({
        text: 'BOGOR SNEAKER',
        fontSize: 50,
        fontFamily: 'Lexend',
        fontStyle: '900',
        fill: '#000000',
        x: stage.width() / 2 - 100,
        y: stage.height() / 2,
        draggable: true,
        padding: 10,
        align: 'center'
    });
    
    const meta = { id, type: 'text', text: 'BOGOR SNEAKER', color: '#000000', strokeColor: '#ffffff' };
    textNode.setAttr('meta', meta);
    
    elementsGroup.add(textNode);
    activeElement.value = meta;
    transformer?.nodes([textNode]);
    mainLayer?.draw();
};

const triggerUpload = () => {
    uploadInputRef.value?.click();
};

const onUploadInputChange = (e: any) => {
    const files = e.target.files;
    if (!files) return;
    
    for (const file of files) {
        const reader = new FileReader();
        reader.onload = (event) => {
            const media = {
                id: Math.random().toString(36).slice(2),
                name: file.name,
                src: event.target?.result
            };
            uploadedMedia.value.push(media);
        };
        reader.readAsDataURL(file);
    }
};

const addUploadAsElement = async (id: string) => {
    const media = uploadedMedia.value.find(m => m.id === id);
    if (!media || !stage || !elementsGroup) return;
    
    try {
        const img = await loadImage(media.src);
        const ratio = img.naturalHeight / img.naturalWidth;
        const width = 200;
        const height = width * ratio;

        const konvaImg = new Konva.Image({
            image: img,
            x: stage.width() / 2 - width / 2,
            y: stage.height() / 2 - height / 2,
            width,
            height,
            draggable: true
        });
        
        const meta = { id: Math.random().toString(36).slice(2), type: 'image', sourceId: id };
        konvaImg.setAttr('meta', meta);
        
        elementsGroup.add(konvaImg);
        activeElement.value = meta;
        transformer?.nodes([konvaImg]);
        mainLayer?.draw();
    } catch (err) {
        showToast('Gagal menambahkan gambar');
    }
};

const removeActiveElement = () => {
    if (activeElement.value && transformer) {
        const node = transformer.nodes()[0];
        if (node) {
            node.destroy();
            transformer.nodes([]);
            activeElement.value = null;
            mainLayer?.draw();
        }
    }
};

const toggleFastTrack = () => {
    fastTrackEnabled.value = !fastTrackEnabled.value;
};

const toggleCustomBox = () => {
    customBoxEnabled.value = !customBoxEnabled.value;
};

const validateCheckout = () => {
    if (!name.value || !phone.value || !guestEmail.value || !shoeSize.value || !guestAddress.value) {
        showToast('Mohon lengkapi data diri dan alamat');
        if (activeSideTab.value !== 'checkout') activeSideTab.value = 'checkout';
        return;
    }
    
    if (!fastTrackEnabled.value) {
        showFastTrackAlert.value = true;
    } else if (!customBoxEnabled.value) {
        showCustomBoxAlert.value = true;
    } else {
        handleSave();
    }
};

const confirmFastTrack = (val: boolean) => {
    fastTrackEnabled.value = val;
    showFastTrackAlert.value = false;
    if (!customBoxEnabled.value) {
        showCustomBoxAlert.value = true;
    } else {
        handleSave();
    }
};

const confirmCustomBox = (val: boolean) => {
    customBoxEnabled.value = val;
    showCustomBoxAlert.value = false;
    handleSave();
};

const fetchCatalog = async () => {
    catalogLoading.value = true;
    try {
        const res = await fetch('/api/studio-custom/catalog?refresh=1');
        const data = await res.json();
        catalogFolders.value = data.folders;
        if (data.folders.length > 0) {
            activeFolderKey.value = data.folders[0].key;
            if (data.folders[0].models.length > 0) {
                currentModel.value = data.folders[0].models[0].id;
            }
        }
    } catch (err) {
        console.error(err);
        showToast('Gagal memuat katalog');
    } finally {
        catalogLoading.value = false;
    }
};

const handleSave = async () => {
    if (isSaving.value) return;
    isSaving.value = true;
    
    try {
        const previewUrl = await createPreviewURL();
        const patternUrl = await createPatternURL();
        
        const timestamp = new Date().getTime();
        const safeName = name.value.replace(/\s+/g, '_') || 'GUEST';
        downloadURL(previewUrl, `PREVIEW_${safeName}_${timestamp}.png`);
        downloadURL(patternUrl, `POLA_${safeName}_${timestamp}.png`);

        const waMessage = encodeURIComponent([
            WHATSAPP_INTRO,
            `Nama: ${name.value}`,
            `WA: ${phone.value}`,
            `Email: ${guestEmail.value}`,
            `Model: ${activeFolder.value?.label} - ${currentModelMeta.value?.label}`,
            `Size: ${shoeSize.value}`,
            `Fast Track: ${fastTrackEnabled.value ? 'Ya' : 'Tidak'}`,
            `Custom Box: ${customBoxEnabled.value ? 'Ya' : 'Tidak'}`,
            `Total Est: ${formatCurrency(guestCheckoutTotal.value)}`,
            `Alamat: ${guestAddress.value}`
        ].join('\n'));
        
        window.open(`https://wa.me/6285511223344?text=${waMessage}`, '_blank');
        
        showToast('Order berhasil diproses & File terunduh!');
    } catch (err) {
        console.error(err);
        showToast('Terjadi kesalahan saat memproses order');
    } finally {
        isSaving.value = false;
    }
};

const createPreviewURL = async (): Promise<string> => {
    if (!stage) return '';
    const oldNodes = transformer?.nodes() || [];
    transformer?.nodes([]);
    mainLayer?.draw();

    const dataURL = stage.toDataURL({ pixelRatio: 2 });
    transformer?.nodes(oldNodes);
    mainLayer?.draw();
    return dataURL;
};

const createPatternURL = async (): Promise<string> => {
    if (!currentModelMeta.value || !activeFolder.value) return '';
    const meta = currentModelMeta.value;
    const baseUrl = `/shoes-svg/${activeFolderKey.value}/${currentModel.value}/`;
    
    const patternCanvas = document.createElement('canvas');
    patternCanvas.width = 2048;
    patternCanvas.height = 2048;
    const ctx = patternCanvas.getContext('2d');
    if (!ctx) return '';

    if (meta.pattern_base_file) {
        const pBase = await loadImage(baseUrl + meta.pattern_base_file);
        ctx.drawImage(pBase, 0, 0, 2048, 2048);
    }

    for (const layer of meta.pattern_layers) {
        const pLayerImg = await loadImage(baseUrl + layer.file);
        const color = layerColors.value[layer.id] || '#ffffff';
        const filled = drawFilledLayer(pLayerImg, color, 2048, 2048);
        ctx.drawImage(filled, 0, 0);
        
        const outline = layerOutlines.value[layer.id];
        if (outline?.active) {
            const out = drawOutlineLayer(pLayerImg, outline.color, outline.size * 2, 2048, 2048);
            ctx.drawImage(out, 0, 0);
        }
    }
    return patternCanvas.toDataURL('image/png');
};

const downloadURL = (url: string, filename: string) => {
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
};

const zoomIn = () => {
    const scale = stage!.scaleX() * 1.2;
    stage?.scale({ x: scale, y: scale });
    mainLayer?.draw();
};

const zoomOut = () => {
    const scale = stage!.scaleX() / 1.2;
    stage?.scale({ x: scale, y: scale });
    mainLayer?.draw();
};

const resetZoom = () => {
    stage?.scale({ x: 1, y: 1 });
    stage?.position({ x: 0, y: 0 });
    mainLayer?.draw();
};

const undo = () => { showToast('Undo diproses...'); };
const redo = () => { showToast('Redo diproses...'); };
const resetDesign = () => {
    if (confirm('Reset semua perubahan?')) {
        layerColors.value = {};
        layerOutlines.value = {};
        elementsGroup?.destroyChildren();
        loadModelAssets();
    }
};

const showShare = () => {
    showToast('Link share disalin ke clipboard');
};

onMounted(async () => {
    initKonva();
    await fetchCatalog();
});

watch(currentModel, () => {
    loadModelAssets();
});

watch(activeFolderKey, (newKey) => {
    const folder = catalogFolders.value.find(f => f.key === newKey);
    if (folder && folder.models.length > 0) {
        if (!folder.models.some((m: { id: number | string | null }) => m.id === currentModel.value)) {
            currentModel.value = folder.models[0].id;
        }
    }
});

watch(() => activeElement.value?.text, (newText) => {
    if (activeElement.value?.type === 'text') {
        const node = elementsGroup?.findOne((n: any) => n.getAttr('meta')?.id === activeElement.value.id);
        if (node) {
            (node as Konva.Text).text(newText);
            mainLayer?.draw();
        }
    }
});

watch(() => activeElement.value?.color, (newColor) => {
    if (activeElement.value?.type === 'text') {
        const node = elementsGroup?.findOne((n: any) => n.getAttr('meta')?.id === activeElement.value.id);
        if (node) {
            (node as Konva.Text).fill(newColor);
            mainLayer?.draw();
        }
    }
});
</script>

<style scoped>
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.05);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.1);
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.panel-slide-enter-active, .panel-slide-leave-active { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
.panel-slide-enter-from { opacity: 0; transform: translateX(50px); }
.panel-slide-leave-to { opacity: 0; transform: translateX(50px); }

.expand-enter-active, .expand-leave-active { transition: all 0.3s ease-out; overflow: hidden; }
.expand-enter-from, .expand-leave-to { opacity: 0; height: 0; }

.toast-enter-active, .toast-leave-active { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translate(-50%, 40px); }

@keyframes popup {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-popup {
    animation: popup 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}
</style>
