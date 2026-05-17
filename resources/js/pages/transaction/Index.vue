<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    transactions: any;
    statuses: any;
}>();

const dateFrom = ref('');
const dateTo = ref('');
const status = ref('');

const headers = [
    { key: 'id', label: 'No' },
    { key: 'cust_email', label: 'Customer Email' },
    { key: 'price', label: 'Price' },
    { key: 'fee', label: 'Fee' },
    { key: 'total_price', label: 'Total Price' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Date' },
    { key: 'actions', label: 'Actions' },
];

const filterTransactions = () => {
    router.get(
        '/transaction',
        {
            date_from: dateFrom.value,
            date_to: dateTo.value,
            status: status.value,
        },
        {
            preserveScroll: true,
        },
    );
};

const viewTransaction = (transactionId: number) => {
    router.get(`/transaction/${transactionId}`);
};

const downloadExcel = () => {
    window.open(`/transaction/excel?date_from=${dateFrom.value}&date_to=${dateTo.value}&status=${status.value}`, '_blank');
};
</script>

<template>
    <Head title="Transactions" />

    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="mb-8 text-2xl font-bold">Transactions</h1>

            <div class="mb-8">
                <div class="mb-4 flex gap-4">
                    <div class="flex-1">
                        <label for="date_from" class="mb-2 block text-sm font-medium text-gray-700">Date From</label>
                        <input
                            id="date_from"
                            v-model="dateFrom"
                            type="date"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex-1">
                        <label for="date_to" class="mb-2 block text-sm font-medium text-gray-700">Date To</label>
                        <input
                            id="date_to"
                            v-model="dateTo"
                            type="date"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex-1">
                        <label for="status" class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                        <select
                            id="status"
                            v-model="status"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option v-for="s in statuses" :key="s" :value="s">
                                {{ s }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button
                            type="button"
                            @click="filterTransactions"
                            class="mr-2 rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Filter
                        </button>

                        <button
                            type="button"
                            @click="downloadExcel"
                            class="rounded-md bg-green-600 px-4 py-2 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                        >
                            Download Excel
                        </button>
                    </div>
                </div>
            </div>

            <PaginatedTable :headers="headers" :paginator="transactions">
                <template #cell-status="{ item }">
                    <span :class="item.status === 'success' ? 'text-green-600' : item.status === 'failed' ? 'text-red-600' : 'text-yellow-600'">
                        {{ item.status }}
                    </span>
                </template>

                <template #cell-actions="{ item }">
                    <button
                        type="button"
                        @click="viewTransaction(item.id)"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        View Details
                    </button>
                </template>
            </PaginatedTable>
        </div>
    </AppLayout>
</template>
