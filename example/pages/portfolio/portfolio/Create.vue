<script setup lang="ts">
import PortfolioForm from '@/components/custom/form/PortfolioForm.vue';
import type { SearchableOption } from '@/components/custom/SearchableInputSelect.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    categories: Array<{ id: number; name: string }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Portfolio', href: '/portfolio' },
    { title: 'Create', href: '/portfolio/create' },
];

const categoryOptions = computed<SearchableOption[]>(() =>
    props.categories.map((c) => ({
        label: c.name,
        value: c.id,
    })),
);

const form = useForm({
    title: '',
    description: '',
    image: null as File | null,
    images: [] as File[],
    link_url: '',
    category_ids: [] as number[],
});

const submit = () => {
    form.post(route('portfolio.store'));
};
</script>

<template>
    <Head title="Create Portfolio" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Tambah Portfolio" description="Buat portfolio baru dan pilih kategorinya." />
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('portfolio.index')">Kembali</Link>
                    </Button>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <PortfolioForm v-model="form" :category-options="categoryOptions" submit-label="Simpan" @submit="submit" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
