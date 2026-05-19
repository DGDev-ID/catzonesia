<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import CategoryForm from '@/components/custom/form/CategoryForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    errors?: Record<string, string[]>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Category', href: '/master/category' },
    { title: 'Create', href: '/master/category/create' },
];

const submit = (data: { name: string }) => {
    router.post('/master/category', data, { preserveScroll: true });
};
</script>

<template>
    <Head title="Create Category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Tambah Category" description="Tambah kategori produk baru." />
                    <Link href="/master/category" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <CategoryForm submit-label="Simpan Category" :errors="errors" @submit="submit" />

            </div>
        </div>
    </AppLayout>
</template>
