<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    packages: any;
}>();

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Name' },
    { key: 'price', label: 'Price' },
    { key: 'is_grooming', label: 'Is Grooming' },
    { key: 'description', label: 'Description' },
    { key: 'actions', label: 'Actions' },
];
</script>

<template>
    <Head title="Package" />

    <AppLayout>
        <div class="container mx-auto py-8">
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Package</h1>
                <Link
                    :href="route('master.package.create')"
                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Add Package
                </Link>
            </div>

            <PaginatedTable :headers="headers" :paginator="packages">
                <template #cell-is_grooming="{ item }">
                    {{ item.is_grooming ? 'Yes' : 'No' }}
                </template>
                <template #cell-actions="{ item }">
                    <Link
                        :href="route('master.package.edit', item.id)"
                        class="mr-2 rounded-md bg-yellow-600 px-3 py-1 text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                    >
                        Edit
                    </Link>
                    <button
                        type="button"
                        class="rounded-md bg-red-600 px-3 py-1 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                        Delete
                    </button>
                </template>
            </PaginatedTable>
        </div>
    </AppLayout>
</template>
