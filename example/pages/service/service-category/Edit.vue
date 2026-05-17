<script setup lang="ts">
import ServiceCategoryForm from '@/components/custom/form/ServiceCategoryForm.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    category: { id: number; title: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Service Category', href: '/service/category' },
    { title: 'Edit', href: `/service/category/${props.category.id}/edit` },
];

const form = useForm({
    title: props.category.title,
});

const submit = () => {
    form.put(route('service.category.update', { category: props.category.id }));
};
</script>

<template>
    <Head title="Edit Service Category" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Edit Service Category" description="Ubah data kategori service." />
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('service.category.index')">Kembali</Link>
                    </Button>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <ServiceCategoryForm v-model="form" submit-label="Update" @submit="submit" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
