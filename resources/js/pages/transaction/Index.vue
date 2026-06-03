<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Eye, Download } from 'lucide-vue-next';
import { formatCurrency } from '@/lib/utils';

const props = defineProps<{
    transactions: any;
    filters: {
        date_from: string | null;
        date_to: string | null;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transaction', href: '/transaction' },
];

const dateFrom = ref(props.filters?.date_from ?? '');
const dateTo = ref(props.filters?.date_to ?? '');

const headers = [
    { key: 'id', label: 'No' },
    { key: 'cust_email', label: 'Email Customer' },
    { key: 'price', label: 'Harga', formatter: formatCurrency },
    { key: 'fee', label: 'Fee', formatter: formatCurrency },
    { key: 'total_price', label: 'Total', formatter: formatCurrency },
    { key: 'created_at', label: 'Tanggal' },
    { key: 'actions', label: 'Aksi' },
];

const filterTransactions = () => {
    router.get(
        '/transaction',
        {
            date_from: dateFrom.value,
            date_to: dateTo.value,
        },
        { preserveScroll: true },
    );
};

const viewTransaction = (transactionId: number) => {
    router.get(`/transaction/${transactionId}`);
};

const downloadExcel = () => {
    const url = route('transaction.export', {
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
    });
    window.open(url, '_blank');
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
