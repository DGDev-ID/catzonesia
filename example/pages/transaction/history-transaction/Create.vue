<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'History Transaction', href: '/transaction' },
    { title: 'Create', href: '/transaction/create' },
];
</script>

<template>
    <Head title="Create Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold">Create Transaction</h1>
                <Button as-child variant="outline">
                    <Link :href="route('transaction.index')">Kembali</Link>
                </Button>
            </div>
            <div class="text-sm text-muted-foreground">Halaman ini belum digunakan.</div>
        </div>
    </AppLayout>
</template>
