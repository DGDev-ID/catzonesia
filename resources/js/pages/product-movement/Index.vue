<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps<{
    products: any;
}>();

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Product Name' },
    { key: 'sku', label: 'SKU' },
    { key: 'stock', label: 'Stock' },
    { key: 'actions', label: 'Actions' },
];

const viewMovement = (productId: number) => {
    router.get(`/product-movement/${productId}`);
};
</script>

<template>
    <Head title="Product Movement" />

    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="mb-8 text-2xl font-bold">Product Movement</h1>

            <PaginatedTable :headers="headers" :paginator="products">
                <template #cell-actions="{ item }">
                    <button
                        type="button"
                        @click="viewMovement(item.id)"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        View Movement
                    </button>
                </template>
            </PaginatedTable>
        </div>
    </AppLayout>
</template>
