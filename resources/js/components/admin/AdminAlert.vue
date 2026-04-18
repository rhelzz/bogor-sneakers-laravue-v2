<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
    >
        <div v-if="message" :class="['admin-alert', alertClass]">
            <i :class="iconClass"></i>
            <span>{{ message }}</span>
        </div>
    </Transition>
</template>

<script lang="ts">
import { computed, defineComponent, type PropType } from 'vue';

type AlertVariant = 'success' | 'error' | 'info';

export default defineComponent({
    name: 'AdminAlert',
    props: {
        message: {
            type: String,
            required: true,
        },
        variant: {
            type: String as PropType<AlertVariant>,
            default: 'info',
        },
    },
    setup(props) {
        const alertClass = computed(() => {
            if (props.variant === 'success') {
                return 'admin-alert-success';
            }

            if (props.variant === 'error') {
                return 'admin-alert-error';
            }

            return 'admin-alert-info';
        });

        const iconClass = computed(() => {
            if (props.variant === 'success') {
                return 'bi bi-check-circle-fill';
            }

            if (props.variant === 'error') {
                return 'bi bi-exclamation-triangle-fill';
            }

            return 'bi bi-info-circle-fill';
        });

        return {
            alertClass,
            iconClass,
        };
    },
});
</script>
