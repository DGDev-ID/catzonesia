<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Planner', href: '/transaction/planner' },
    { title: 'Create', href: '/transaction/planner/create' },
];
</script>

<template>
    <Head title="Create Planner" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 text-sm text-muted-foreground">Planner belum dibuat.</div>
    </AppLayout>
</template>
