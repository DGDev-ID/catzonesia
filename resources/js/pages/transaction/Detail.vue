<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    transaction: any;
}>();

const headers = [
    { key: 'id', label: 'No' },
    { key: 'product.name', label: 'Product Name' },
    { key: 'package.name', label: 'Package Name' },
    { key: 'amount', label: 'Amount' },
    { key: 'unit.name', label: 'Unit' },
    { key: 'price', label: 'Price' },
    { key: 'notes', label: 'Notes' },
];
</script>

<template>
    <Head title="Transaction Detail" />

    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="mb-8 text-2xl font-bold">Transaction Detail - #{{ props.transaction.id }}</h1>

            <div class="mb-8">
                <h2 class="mb-4 text-xl font-semibold">Transaction Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Customer Email</label>
                        <p class="text-gray-900">{{ props.transaction.cust_email }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Total Price</label>
                        <p class="text-gray-900">{{ props.transaction.total_price }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Fee</label>
                        <p class="text-gray-900">{{ props.transaction.fee }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Price</label>
                        <p class="text-gray-900">{{ props.transaction.price }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                        <p
                            :class="
                                props.transaction.status === 'success'
                                    ? 'text-green-600'
                                    : props.transaction.status === 'failed'
                                      ? 'text-red-600'
                                      : 'text-yellow-600'
                            "
                        >
                            {{ props.transaction.status }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Created At</label>
                        <p class="text-gray-900">{{ props.transaction.created_at }}</p>
                    </div>
                </div>
            </div>

            <div v-if="props.transaction.transaction_grooming" class="mb-8">
                <h2 class="mb-4 text-xl font-semibold">Grooming Information</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Owner Name</label>
                        <p class="text-gray-900">{{ props.transaction.transaction_grooming.owner_name }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Owner Phone</label>
                        <p class="text-gray-900">{{ props.transaction.transaction_grooming.owner_phone }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Cat Name</label>
                        <p class="text-gray-900">{{ props.transaction.transaction_grooming.cat_name }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Cat Age</label>
                        <p class="text-gray-900">{{ props.transaction.transaction_grooming.cat_age }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Cat Estimated Weight</label>
                        <p class="text-gray-900">{{ props.transaction.transaction_grooming.cat_estimated_weight }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
                        <p class="text-gray-900">{{ props.transaction.transaction_grooming.notes }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="mb-4 text-xl font-semibold">Transaction Details</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-muted/50">
                            <tr class="text-muted-foreground">
                                <th scope="col" class="px-6 py-4 text-left font-medium">No</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Product/Package</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Amount</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Unit</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Price</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Notes</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item, index) in transaction.transaction_details" :key="item.id" class="border-t transition hover:bg-muted/40">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4">
                                    {{ item.product?.name || item.package?.name }}
                                </td>
                                <td class="px-6 py-4">{{ item.amount }}</td>
                                <td class="px-6 py-4">{{ item.unit?.name }}</td>
                                <td class="px-6 py-4">{{ item.price }}</td>
                                <td class="px-6 py-4">{{ item.notes }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="mb-4 text-xl font-semibold">Transaction Logs</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-muted/50">
                            <tr class="text-muted-foreground">
                                <th scope="col" class="px-6 py-4 text-left font-medium">No</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Status</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Notes</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Created At</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item, index) in transaction.transaction_logs" :key="item.id" class="border-t transition hover:bg-muted/40">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4">{{ item.status }}</td>
                                <td class="px-6 py-4">{{ item.notes }}</td>
                                <td class="px-6 py-4">{{ item.created_at }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
