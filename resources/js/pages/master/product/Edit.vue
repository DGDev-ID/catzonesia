<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import ProductForm from '@/components/custom/form/ProductForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    product: any;
    categories: any[];
    units: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Product', href: '/master/product' },
    { title: 'Edit', href: `/master/product/${props.product.id}/edit` },
];

const submit = (data: any) => {
    const payload: any = {
        ...data,
        unit_converters: data.unitConverters,
        _method: 'put',
    };
    delete payload.unitConverters;

    router.post(`/master/product/${props.product.id}`, payload, { preserveScroll: true, forceFormData: true });
};
</script>

<template>
    <Head title="Edit Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Edit Product" description="Perbarui data produk yang sudah ada." />
                    <Link href="/master/product" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <ProductForm
                    :categories="product.categories"
                    :units="$props.units"
                    :initial-data="{
                        name: product.name,
                        sku: product.sku,
                        price: product.price,
                        stock: product.stock,
                        base_unit_id: product.base_unit_id,
                        description: product.description,
                            img_url: product.img_url,
                        is_display: product.is_display,
                        categories: product.categories.map((c: any) => c.id),
                        unitConverters: product.product_unit_converters.map((conv: any) => ({ 
                            unit_from_id: conv.unit_from_id, 
                            unit_to_id: conv.unit_to_id, 
                            multiplier: conv.multiplier 
                        })),
                    }"
                    submit-label="Update Product"
                    back-url="/master/product"
                    @submit="submit"
                />

            </div>
        </div>
    </AppLayout>
</template>
