<template>
    <section class="admin-page-header">
        <div>
            <h1 class="admin-page-title">{{ title }}</h1>
            <p v-if="description" class="admin-page-subtitle">
                {{ description }}
            </p>
        </div>

        <div v-if="$slots.actions" class="admin-page-header-actions">
            <slot name="actions" />
        </div>
    </section>
</template>

<script setup lang="ts">
withDefaults(
    defineProps<{
        title: string;
        description?: string;
    }>(),
    {
        description: '',
    },
);
</script>
