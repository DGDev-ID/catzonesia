<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Eye, Download } from 'lucide-vue-next';

const props = defineProps<{
    transactions: any;
    statuses: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transaction', href: '/transaction' },
];

const dateFrom = ref('');
const dateTo = ref('');
const status = ref('');

const headers = [
    { key: 'id', label: 'No' },
    { key: 'cust_email', label: 'Email Customer' },
    { key: 'price', label: 'Harga' },
    { key: 'fee', label: 'Fee' },
    { key: 'total_price', label: 'Total' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Tanggal' },
    { key: 'actions', label: 'Aksi' },
];

const filterTransactions = () => {
    router.get(
        '/transaction',
        {
            date_from: dateFrom.value,
            date_to: dateTo.value,
            status: status.value,
        },
        { preserveScroll: true },
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

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <Heading variant="small" title="Transaksi" description="Riwayat seluruh transaksi." />
                    <button
                        type="button"
                        @click="downloadExcel"
                        class="inline-flex items-center gap-2 justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:opacity-90"
                    >
                        <Download :size="16" />
                        Download Excel
                    </button>
                </div>

                <!-- Filter -->
                <div class="rounded-2xl border bg-background shadow-sm p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="date_from" class="mb-2 block text-sm font-medium">Tanggal Dari</label>
                            <input
                                id="date_from"
                                v-model="dateFrom"
                                type="date"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>
                        <div>
                            <label for="date_to" class="mb-2 block text-sm font-medium">Tanggal Sampai</label>
                            <input
                                id="date_to"
                                v-model="dateTo"
                                type="date"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>
                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium">Status</label>
                            <select
                                id="status"
                                v-model="status"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="">Semua Status</option>
                                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button
                            type="button"
                            @click="filterTransactions"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                        >
                            Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <PaginatedTable :headers="headers" :paginator="transactions">
                        <template #cell-status="{ item }">
                            <span
                                :class="{
                                    'bg-green-100 text-green-700': item.status === 'success',
                                    'bg-red-100 text-red-700': item.status === 'failed',
                                    'bg-yellow-100 text-yellow-700': item.status === 'pending',
                                }"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                            >
                                {{ item.status }}
                            </span>
                        </template>

                        <template #cell-actions="{ item }">
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    @click="viewTransaction(item.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition"
                                    title="Lihat Detail"
                                >
                                    <Eye :size="16" />
                                </button>
                            </div>
                        </template>
                    </PaginatedTable>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
