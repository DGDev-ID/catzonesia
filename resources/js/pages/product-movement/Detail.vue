<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    product: any;
    movements: any;
    units: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Product Movement', href: '/product-movement' },
    { title: props.product.name, href: `/product-movement/${props.product.id}` },
];

const quantity = ref(0);
const unitId = ref<number | null>(null);
const note = ref('');

const headers = [
    { key: 'id', label: 'No' },
    { key: 'type', label: 'Tipe' },
    { key: 'quantity', label: 'Jumlah' },
    { key: 'unit.name', label: 'Unit' },
    { key: 'price', label: 'Harga' },
    { key: 'note', label: 'Catatan' },
    { key: 'created_at', label: 'Tanggal' },
];

const adjustStock = () => {
    if (quantity.value === 0 || !unitId.value) {
        alert('Masukkan jumlah dan pilih unit');
        return;
    }

    router.post(
        '/api/product-movement/adjust',
        {
            product_id: props.product.id,
            type: 'adjustment',
            quantity: Math.abs(quantity.value),
            unit_id: unitId.value,
            note: note.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        },
    );
};
</script>

<template>
    <Head title="Product Movement" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" :title="props.product.name" description="Pergerakan stok & penyesuaian." />
                    <Link href="/product-movement" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <!-- Adjust Stock Card -->
                <div class="rounded-2xl border bg-background shadow-sm p-8">
                    <h3 class="mb-6 text-base font-semibold">Penyesuaian Stok</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="quantity" class="mb-1 block text-sm font-medium">Jumlah</label>
                            <input
                                id="quantity"
                                v-model="quantity"
                                type="number"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>
                        <div>
                            <label for="unit_id" class="mb-1 block text-sm font-medium">Unit</label>
                            <select
                                id="unit_id"
                                v-model="unitId"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="">Pilih Unit</option>
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="note" class="mb-1 block text-sm font-medium">Catatan</label>
                            <input
                                id="note"
                                v-model="note"
                                type="text"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>
                    </div>
                    <div class="mt-4">
                        <button
                            type="button"
                            @click="adjustStock"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                        >
                            Sesuaikan Stok
                        </button>
                    </div>
                </div>

                <!-- Movement Table -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold">Riwayat Pergerakan</h3>
                    </div>
                    <PaginatedTable :headers="headers" :paginator="movements">
                        <template #cell-type="{ item }">
                            <span
                                :class="item.type === 'inbound' ? 'bg-green-100 text-green-700' : item.type === 'outbound' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                            >
                                {{ item.type }}
                            </span>
                        </template>
                    </PaginatedTable>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
