<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import SearchableInputSelect, { type SearchableOption } from '@/components/custom/SearchableInputSelect.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type TransactionRow = {
    id: number;
    ticket_id: string;
    cust_email: string;
    cust_name: string;
    cust_phone: string;
    start_date: string;
    end_date: string;
    total_price: string | number;
    is_ticket_closed: boolean | number;
    last_status: 'pending' | 'success' | 'failed' | null;
    service_title: string | null;
    service_package_title: string | null;
    service_id: number | null;
};

type Paginator<T> = {
    data: T[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        per_page: number;
        to: number | null;
        total: number;
    };
};

const props = defineProps<{
    transactions: Paginator<TransactionRow>;
    services: Array<{ id: number; title: string }>;
    statusOptions: Array<'pending' | 'success' | 'failed'>;
    filters: {
        status: string | null;
        from_date: string | null;
        to_date: string | null;
        service_id: number | null;
        search: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'History Transaction', href: '/transaction' }];

const search = ref(props.filters.search ?? '');
const status = ref<string | number | null>(props.filters.status ?? null);
const serviceId = ref<string | number | null>(props.filters.service_id ?? null);
const fromDate = ref(props.filters.from_date ?? '');
const toDate = ref(props.filters.to_date ?? '');

const statusSelectOptions = computed<SearchableOption[]>(() =>
    props.statusOptions.map((s) => ({
        label: s,
        value: s,
    })),
);

const serviceSelectOptions = computed<SearchableOption[]>(() =>
    props.services.map((s) => ({
        label: s.title,
        value: s.id,
    })),
);

const applyFilters = () => {
    router.get(
        route('transaction.index'),
        {
            search: search.value,
            status: status.value || null,
            service_id: serviceId.value || null,
            from_date: fromDate.value || null,
            to_date: toDate.value || null,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const resetFilters = () => {
    search.value = '';
    status.value = null;
    serviceId.value = null;
    fromDate.value = '';
    toDate.value = '';
    applyFilters();
};

const headers = [
    { key: 'ticket_id', label: 'Ticket' },
    { key: 'customer', label: 'Customer' },
    { key: 'service', label: 'Service' },
    { key: 'date', label: 'Tanggal' },
    { key: 'total_price', label: 'Total' },
    { key: 'last_status', label: 'Status' },
];
</script>

<template>
    <Head title="History Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="History Transaction" description="Lihat riwayat transaksi dan lakukan filter." />
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <div class="grid gap-4 md:grid-cols-12">
                    <div class="md:col-span-4">
                        <Label>Cari</Label>
                        <Input v-model="search" placeholder="ticket / nama / email / phone" class="rounded-xl" />
                    </div>

                    <div class="md:col-span-3">
                        <Label>Status</Label>
                        <SearchableInputSelect v-model="status" :options="statusSelectOptions" placeholder="Semua" />
                    </div>

                    <div class="md:col-span-5">
                        <Label>Service</Label>
                        <SearchableInputSelect v-model="serviceId" :options="serviceSelectOptions" placeholder="Semua" />
                    </div>

                    <div class="md:col-span-3">
                        <Label>From Date</Label>
                        <Input v-model="fromDate" type="date" class="rounded-xl" />
                    </div>

                    <div class="md:col-span-3">
                        <Label>To Date</Label>
                        <Input v-model="toDate" type="date" class="rounded-xl" />
                    </div>

                    <div class="flex gap-2 md:col-span-6 md:items-end">
                        <Button type="button" @click="applyFilters" variant="outline" class="rounded-xl">Terapkan</Button>
                        <Button type="button" @click="resetFilters" variant="outline" class="rounded-xl">Reset</Button>
                    </div>
                    </div>
                </div>

                <PaginatedTable :headers="headers" :paginator="transactions">
                <template #cell-customer="{ item }">
                    <div class="space-y-1">
                        <div class="font-medium">{{ item.cust_name }}</div>
                        <div class="text-xs text-muted-foreground">{{ item.cust_email }} | {{ item.cust_phone }}</div>
                    </div>
                </template>

                <template #cell-service="{ item }">
                    <div class="space-y-1">
                        <div class="font-medium">{{ item.service_title ?? '-' }}</div>
                        <div class="text-xs text-muted-foreground">{{ item.service_package_title ?? '-' }}</div>
                    </div>
                </template>

                <template #cell-date="{ item }">
                    <div class="text-sm">{{ item.start_date }} - {{ item.end_date }}</div>
                </template>

                <template #cell-last_status="{ item }">
                    <span class="rounded-full border border-sidebar-border/70 bg-muted/30 px-2 py-0.5 text-xs">
                        {{ item.last_status ?? '-' }}
                    </span>
                </template>
            </PaginatedTable>
            </div>
        </div>
    </AppLayout>
</template>
