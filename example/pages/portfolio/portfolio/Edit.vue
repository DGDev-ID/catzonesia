<script setup lang="ts">
import PortfolioForm from '@/components/custom/form/PortfolioForm.vue';
import type { SearchableOption } from '@/components/custom/SearchableInputSelect.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    portfolio: {
        id: number;
        title: string;
        description: string;
        image: string | null;
        link_url: string | null;
        images: { id: number; img_url: string }[];
    };
    categories: Array<{ id: number; name: string }>;
    selectedCategoryIds: number[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Portfolio', href: '/portfolio' },
    { title: 'Edit', href: `/portfolio/${props.portfolio.id}/edit` },
];

const categoryOptions = computed<SearchableOption[]>(() =>
    props.categories.map((c) => ({
        label: c.name,
        value: c.id,
    })),
);

const form = useForm({
    title: props.portfolio.title,
    description: props.portfolio.description,
    image: null as File | null,
    images: [] as File[],
    remove_image: false,
    link_url: props.portfolio.link_url ?? '',
    category_ids: props.selectedCategoryIds ?? [],
});

const submit = () => {
    form.post(route('portfolio.update', { portfolio: props.portfolio.id }));
};

function removeExistingImage(imageId: number) {
    router.delete(route('portfolio.images.destroy', { portfolio: props.portfolio.id, image: imageId }), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Edit Portfolio" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Edit Portfolio" description="Ubah data portfolio dan kategorinya." />
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('portfolio.index')">Kembali</Link>
                    </Button>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <PortfolioForm
                        v-model="form"
                        :category-options="categoryOptions"
                        :existing-cover-image="portfolio.image"
                        :existing-images="portfolio.images"
                        submit-label="Update"
                        @submit="submit"
                        @remove-existing-image="removeExistingImage"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
