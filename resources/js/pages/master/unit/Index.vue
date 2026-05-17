<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    units: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master',
    },
    {
        title: 'Unit',
        href: '/master/unit',
    },
];

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Name' },
    { key: 'actions', label: 'Actions' },
];

const deleteUnit = (id: number) => {
    if (confirm('Are you sure you want to delete this unit?')) {
        router.delete(`/api/master/unit/${id}`);
    }
};
</script>

<template>
    <Head title="Unit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Unit Management</h1>
                <Link href="/master/unit/create" class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"> Add Unit </Link>
            </div>

            <PaginatedTable :headers="headers" :paginator="units">
                <template #cell-actions="{ item }">
                    <div class="flex gap-2">
                        <Link href="/master/unit/edit/:id" :params="{ id: item.id }" class="text-blue-500 hover:underline"> Edit </Link>
                        <button @click="deleteUnit(item.id)" class="text-red-500 hover:underline">Delete</button>
                    </div>
                </template>
            </PaginatedTable>
        </div>
    </AppLayout>
</template>
