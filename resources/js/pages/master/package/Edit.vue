<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import PackageForm from '@/components/custom/form/PackageForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    package: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Package', href: '/master/package' },
    { title: 'Edit', href: `/master/package/${props.package.id}/edit` },
];

const submit = (data: any) => {
    router.put(`/master/package/${props.package.id}`, data, { preserveScroll: true });
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
                    :initial-data="{
                        name: package.name,
                        price: package.price,
                        is_grooming: package.is_grooming,
                        description: package.description,
                    }"
                    submit-label="Update Package"
                    @submit="submit"
                />

            </div>
        </div>
    </AppLayout>
</template>
