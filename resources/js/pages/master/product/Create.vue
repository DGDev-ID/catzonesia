<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import ProductForm from '@/components/custom/form/ProductForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    categories: any[];
    units: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Product', href: '/master/product' },
    { title: 'Create', href: '/master/product/create' },
];

const submit = (data: any) => {
    const payload: any = {
        ...data,
        unit_converters: data.unitConverters,
    };
    delete payload.unitConverters;

    router.post('/master/product', payload, { preserveScroll: true, forceFormData: true });
};
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Tambah Product" description="Tambah produk baru ke dalam sistem." />
                    <Link href="/master/product" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <ProductForm
                    :categories="$props.categories"
                    :units="$props.units"
                    submit-label="Simpan Product"
                    back-url="/master/product"
                    @submit="submit"
                />

            </div>
        </div>
    </AppLayout>
</template>
