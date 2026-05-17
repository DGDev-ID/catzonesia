<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Eye } from 'lucide-vue-next';

const props = defineProps<{
    products: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Product Movement', href: '/product-movement' },
];

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Nama Produk' },
    { key: 'sku', label: 'SKU' },
    { key: 'stock', label: 'Stok' },
    { key: 'actions', label: 'Aksi' },
];

const viewMovement = (productId: number) => {
    router.get(`/product-movement/${productId}`);
};
</script>

<template>
    <Head title="Product Movement" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <Heading variant="small" title="Product Movement" description="Kelola pergerakan stok produk." />

                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <PaginatedTable :headers="headers" :paginator="products">
                        <template #cell-actions="{ item }">
                            <button
                                type="button"
                                @click="viewMovement(item.id)"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-500 hover:text-white transition"
                                title="Lihat Pergerakan"
                            >
                                <Eye class="h-4 w-4" />
                            </button>
                        </template>
                    </PaginatedTable>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
