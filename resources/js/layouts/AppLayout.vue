<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { notyf } from '@/lib/notyf';
import type { BreadcrumbItemType } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();

watch(
    () => page.props.errors,
    (errors) => {
        if (!errors || typeof errors !== 'object') return;
        Object.values(errors as Record<string, string>).forEach((message) => {
            notyf.error(message);
        });
    },
    { deep: true },
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
