<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import UnitForm from '@/components/custom/form/UnitForm.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    unit: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Unit', href: '/master/unit' },
    { title: 'Edit', href: `/master/unit/${props.unit.id}/edit` },
];

const submit = (data: { name: string }) => {
    router.put(`/master/unit/${props.unit.id}`, data, { preserveScroll: true });
};
</script>

<template>
    <Head title="Edit Unit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Edit Unit" description="Perbarui data unit yang sudah ada." />
                    <Link href="/master/unit" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <UnitForm
                    :initial-name="unit.name"
                    submit-label="Update Unit"
                    @submit="submit"
                />

            </div>
        </div>
    </AppLayout>
</template>
