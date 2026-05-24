<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import PackageForm from '@/components/custom/form/PackageForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    products: any[];
    units: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Package', href: '/master/package' },
    { title: 'Create', href: '/master/package/create' },
];

const submit = (data: any) => {
    router.post('/master/package', data, { preserveScroll: true });
};
</script>

<template>
    <Head title="Create Package" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Tambah Package" description="Tambah paket layanan baru." />
                    <Link href="/master/package" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <PackageForm
                    :products="products"
                    :units="units"
                    submit-label="Simpan Package"
                    @submit="submit"
                />

            </div>
        </div>
    </AppLayout>
</template>
