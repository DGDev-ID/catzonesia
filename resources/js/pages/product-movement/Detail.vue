<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    product: any;
    movements: any;
    units: any;
}>();

const quantity = ref(0);
const unitId = ref<number | null>(null);
const note = ref('');

const headers = [
    { key: 'id', label: 'No' },
    { key: 'type', label: 'Type' },
    { key: 'quantity', label: 'Quantity' },
    { key: 'unit.name', label: 'Unit' },
    { key: 'price', label: 'Price' },
    { key: 'note', label: 'Note' },
    { key: 'created_at', label: 'Date' },
];

const adjustStock = () => {
    if (quantity.value === 0 || !unitId.value) {
        alert('Please enter quantity and select unit');
        return;
    }

    const type = quantity.value > 0 ? 'adjustment' : 'adjustment';
    const absQuantity = Math.abs(quantity.value);

    router.post(
        '/api/product-movement/adjust',
        {
            product_id: props.product.id,
            type: type,
            quantity: absQuantity,
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

    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="mb-8 text-2xl font-bold">{{ props.product.name }}</h1>

            <div class="mb-8">
                <h2 class="mb-4 text-xl font-semibold">Adjust Stock</h2>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label for="quantity" class="mb-2 block text-sm font-medium text-gray-700">Quantity</label>
                        <input
                            id="quantity"
                            v-model="quantity"
                            type="number"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex-1">
                        <label for="unit_id" class="mb-2 block text-sm font-medium text-gray-700">Unit</label>
                        <select
                            id="unit_id"
                            v-model="unitId"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select Unit</option>
                            <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                {{ unit.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex-1">
                        <label for="note" class="mb-2 block text-sm font-medium text-gray-700">Note</label>
                        <input
                            id="note"
                            v-model="note"
                            type="text"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex items-end">
                        <button
                            type="button"
                            @click="adjustStock"
                            class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Adjust Stock
                        </button>
                    </div>
                </div>
            </div>

            <PaginatedTable :headers="headers" :paginator="movements">
                <template #cell-type="{ item }">
                    <span :class="item.type === 'inbound' ? 'text-green-600' : item.type === 'outbound' ? 'text-red-600' : 'text-yellow-600'">
                        {{ item.type }}
                    </span>
                </template>
            </PaginatedTable>
        </div>
    </AppLayout>
</template>
