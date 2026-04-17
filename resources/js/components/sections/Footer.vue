<template>
    <!-- FOOTER -->
    <footer class="pattern-koinobori bg-sumi px-6 py-16 text-washi lg:px-16">
        <div class="mx-auto max-w-7xl">
            <!-- Big Text -->
            <div class="mb-16">
                <h2
                    class="font-heading text-6xl leading-none font-bold opacity-10 lg:text-8xl"
                >
                    BOGOR
                </h2>
                <h2
                    class="font-heading -mt-4 text-6xl leading-none font-bold lg:-mt-8 lg:text-8xl"
                >
                    SNEAKERS
                </h2>
                <p class="mt-4 text-sm tracking-widest text-washi/40">
                    SINCE 2019 - INDONESIA
                </p>
            </div>

            <!-- Footer Grid -->
            <div
                class="grid grid-cols-1 gap-12 border-b border-washi/10 pb-12 md:grid-cols-2 lg:grid-cols-4"
            >
                <!-- Store -->
                <div>
                    <p class="mb-4 text-xs tracking-widest text-washi/40">
                        STORE
                    </p>
                    <div class="space-y-2 text-sm">
                        <p>Jl. Pajajaran No.88</p>
                        <p>Bogor, Jawa Barat</p>
                        <p>16143 Indonesia</p>
                    </div>
                </div>

                <!-- Hours -->
                <div>
                    <p class="mb-4 text-xs tracking-widest text-washi/40">
                        JAM BUKA
                    </p>
                    <div class="space-y-2 text-sm">
                        <p>Senin-Jumat: 09.00-21.00</p>
                        <p>Sabtu-Minggu: 10.00-22.00</p>
                    </div>
                </div>

                <!-- Navigation -->
                <div>
                    <p class="mb-4 text-xs tracking-widest text-washi/40">
                        NAVIGASI
                    </p>
                    <div class="space-y-2 text-sm">
                        <a
                            href="#katalog"
                            class="block transition-colors hover:text-matcha"
                            >Katalog</a
                        >
                        <a
                            href="#po-tracker"
                            class="block transition-colors hover:text-matcha"
                            >Live PO</a
                        >
                        <a
                            href="#tiktok"
                            class="block transition-colors hover:text-matcha"
                            >TikTok Feed</a
                        >
                        <a
                            href="#galeri"
                            class="block transition-colors hover:text-matcha"
                            >Galeri DIY</a
                        >
                    </div>
                </div>

                <!-- Social -->
                <div>
                    <p class="mb-4 text-xs tracking-widest text-washi/40">
                        SOSIAL
                    </p>
                    <div class="space-y-2 text-sm">
                        <a
                            href="#"
                            class="flex items-center gap-2 transition-colors hover:text-matcha"
                        >
                            <i class="bi bi-instagram"></i> @bogorsneakers
                        </a>
                        <a
                            href="#"
                            class="flex items-center gap-2 transition-colors hover:text-matcha"
                        >
                            <i class="bi bi-tiktok"></i> @bogorsneakers
                        </a>
                        <a
                            href="#"
                            class="flex items-center gap-2 transition-colors hover:text-matcha"
                        >
                            <i class="bi bi-whatsapp"></i> 0812-XXXX-XXXX
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom -->
            <div
                class="flex flex-col items-center justify-between pt-8 md:flex-row"
            >
                <p class="text-xs text-washi/40">
                    2026 Bogorsneakers. All rights reserved.
                </p>
                <div class="mt-4 flex items-center gap-4 md:mt-0">
                    <span class="text-xs text-washi/40">Made with</span>
                    <span class="text-matcha">日本美</span>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts"></script>

<style scoped>
.pattern-koinobori {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='100' viewBox='0 0 120 100'%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='1.2' opacity='0.15' d='M10 30 Q 30 20 50 30 T 90 30'/%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='1' opacity='0.11' d='M10 55 Q 30 45 50 55 T 90 55'/%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='0.8' opacity='0.08' d='M10 80 Q 30 70 50 80 T 90 80'/%3E%3C/svg%3E");
    background-size: 120px 100px;
}
</style>
