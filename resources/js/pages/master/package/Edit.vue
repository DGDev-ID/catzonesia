<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import PackageForm from '@/components/custom/form/PackageForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    package: any;
    products: any[];
    units: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Package', href: '/master/package' },
    { title: 'Edit', href: `/master/package/${props.package.id}/edit` },
];

const submit = (data: any) => {
    router.post(
        `/master/package/${props.package.id}`,
        {
            ...data,
            _method: 'put',
        },
        { preserveScroll: true, forceFormData: true },
    );
};
</script>

<template>
    <Head title="Edit Package" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Edit Package" description="Perbarui data paket layanan yang sudah ada." />
                    <Link href="/master/package" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <PackageForm
                    :products="products"
                    :units="units"
                    :initial-data="{
                        name: package.name,
                        price: package.price,
                        is_grooming: package.is_grooming,
                        description: package.description,
                        img_url: package.img_url,
                        products: package.products.map((p: any) => ({
                            product_id: p.id,
                            quantity: p.pivot.quantity,
                            unit_id: p.pivot.unit_id,
                        })),
                    }"
                    submit-label="Update Package"
                    @submit="submit"
                />

            </div>
        </div>
    </AppLayout>
</template>
