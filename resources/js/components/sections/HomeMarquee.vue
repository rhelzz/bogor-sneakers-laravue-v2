<template>
    <div 
        :class="['relative w-full overflow-hidden py-4 sm:py-5 select-none border-y border-sumi/5', bgColor, textColor]"
        style="mask-image: linear-gradient(to right, transparent 0%, #000 15%, #000 85%, transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 15%, #000 85%, transparent 100%);"
    >
        <div class="relative flex w-max marquee-container">
            <!-- Track 1 -->
            <div
                :class="[
                    'track-animation flex items-center gap-8 sm:gap-12 whitespace-nowrap shrink-0 pr-8 sm:pr-12',
                    reversed ? 'direction-reverse' : ''
                ]"
            >
                <div 
                    v-for="(item, idx) in items" 
                    :key="`t1-${idx}`"
                    class="flex items-center gap-8 sm:gap-12"
                >
                    <!-- Alternate between solid and elegant outlined font for a high-street premium vibe -->
                    <span 
                        class="font-heading text-xs sm:text-sm lg:text-base font-black tracking-[0.25em] uppercase hover-scale"
                        :class="idx % 2 === 1 ? 'text-stroke-washi' : ''"
                    >
                        {{ item }}
                    </span>
                    <span 
                        class="text-[10px] sm:text-xs opacity-80 scale-90 hover-rotate" 
                        :class="separatorColor || 'text-washi/55'"
                    >
                        ✦
                    </span>
                </div>
            </div>

            <!-- Track 2 (Exact duplicate for seamless looping) -->
            <div
                :class="[
                    'track-animation flex items-center gap-8 sm:gap-12 whitespace-nowrap shrink-0 pr-8 sm:pr-12',
                    reversed ? 'direction-reverse' : ''
                ]"
                aria-hidden="true"
            >
                <div 
                    v-for="(item, idx) in items" 
                    :key="`t2-${idx}`"
                    class="flex items-center gap-8 sm:gap-12"
                >
                    <span 
                        class="font-heading text-xs sm:text-sm lg:text-base font-black tracking-[0.25em] uppercase hover-scale"
                        :class="idx % 2 === 1 ? 'text-stroke-washi' : ''"
                    >
                        {{ item }}
                    </span>
                    <span 
                        class="text-[10px] sm:text-xs opacity-80 scale-90 hover-rotate" 
                        :class="separatorColor || 'text-washi/55'"
                    >
                        ✦
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    items: string[];
    reversed?: boolean;
    bgColor: string;
    textColor: string;
    separatorColor?: string;
}>();
</script>

<style scoped>
.track-animation {
    animation: marquee 25s linear infinite;
    /* Force GPU rendering / compositing layer promotion */
    will-change: transform;
    transform: translate3d(0, 0, 0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.track-animation.direction-reverse {
    animation-direction: reverse;
}

/* Elegant text outline styling for high-end streetwear feel */
.text-stroke-washi {
    color: transparent;
    -webkit-text-stroke: 1px currentColor;
    opacity: 0.65;
}

.hover-scale {
    display: inline-block;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    /* Improve subpixel rendering during transform */
    backface-visibility: hidden;
}

.hover-rotate {
    display: inline-block;
    transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
    backface-visibility: hidden;
}

/* 
 * Isolating hover states to devices that support a pointing pointer (e.g. mouse/trackpad).
 * This completely avoids performance lag, paint redraws, and sticky hover behavior on mobile.
 */
@media (hover: hover) {
    .marquee-container:hover .track-animation {
        animation-play-state: paused;
    }
    .hover-scale:hover {
        transform: scale(1.05);
    }
    .hover-rotate:hover {
        transform: rotate(180deg);
    }
}

@keyframes marquee {
    0% {
        transform: translate3d(0, 0, 0);
    }
    100% {
        transform: translate3d(-100%, 0, 0);
    }
}
</style>
