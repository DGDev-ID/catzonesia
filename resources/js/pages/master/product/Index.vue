<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps<{
    products: any;
}>();

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Name' },
    { key: 'sku', label: 'SKU' },
    { key: 'price', label: 'Price' },
    { key: 'stock', label: 'Stock' },
    { key: 'actions', label: 'Actions' },
];

const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(`/api/products/${id}`);
    }
};
</script>

<template>
    <Head title="Products" />

    <AppLayout>
        <div class="container mx-auto py-8">
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Products</h1>
                <button
                    type="button"
                    @click="router.visit('/products/create')"
                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Add Product
                </button>
            </div>

            <PaginatedTable :headers="headers" :paginator="products">
                <template #cell-actions="{ item }">
                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="router.visit(`/products/edit/${item.id}`)"
                            class="rounded-md bg-yellow-600 px-3 py-1 text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            @click="handleDelete(item.id)"
                            class="rounded-md bg-red-600 px-3 py-1 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                        >
                            Delete
                        </button>
                    </div>
                </template>
            </PaginatedTable>
        </div>
    </AppLayout>
</template>
