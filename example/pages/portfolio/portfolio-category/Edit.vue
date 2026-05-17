<script setup lang="ts">
import PortfolioCategoryForm from '@/components/custom/form/PortfolioCategoryForm.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    category: {
        id: number;
        name: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Portfolio Category', href: '/portfolio/category' },
    { title: 'Edit', href: `/portfolio/category/${props.category.id}/edit` },
];

const form = useForm({
    name: props.category.name,
});

const submit = () => {
    form.put(route('portfolio.category.update', { category: props.category.id }));
};
</script>

<template>
    <Head title="Edit Portfolio Category" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Edit Portfolio Category" description="Ubah data kategori portfolio." />
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('portfolio.category.index')">Kembali</Link>
                    </Button>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <PortfolioCategoryForm v-model="form" submit-label="Update" @submit="submit" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
