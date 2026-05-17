<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Role Permission', href: '/permission' },
    { title: 'Create', href: '/permission/create' },
];
</script>

<template>
    <Head title="Create Role Permission" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold">Role Permission</h1>
                <Button as-child variant="outline">
                    <Link :href="route('permission.index')">Kembali</Link>
                </Button>
            </div>
            <div class="text-sm text-muted-foreground">Pilih role pada halaman index untuk melakukan plotting permission.</div>
        </div>
    </AppLayout>
</template>
