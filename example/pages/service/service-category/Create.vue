<script setup lang="ts">
import ServiceCategoryForm from '@/components/custom/form/ServiceCategoryForm.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Service Category', href: '/service/category' },
    { title: 'Create', href: '/service/category/create' },
];

const form = useForm({
    title: '',
});

const submit = () => {
    form.post(route('service.category.store'));
};
</script>

<template>
    <Head title="Create Service Category" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Tambah Service Category" description="Buat kategori baru untuk service." />
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('service.category.index')">Kembali</Link>
                    </Button>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <ServiceCategoryForm v-model="form" submit-label="Simpan" @submit="submit" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
