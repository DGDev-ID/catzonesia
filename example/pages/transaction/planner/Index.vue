<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    planner_count: number;
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
    filters: {
        search: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Planner', href: '/transaction/planner' }];

const search = ref(props.filters.search ?? '');

const applyFilters = () => {
    router.get(
        route('transaction.planner.index'),
        { search: search.value || null },
        { preserveState: true, replace: true },
    );
};

const resetFilters = () => {
    search.value = '';
    applyFilters();
};

const headers = [
    { key: 'ticket_id', label: 'Ticket' },
    { key: 'customer', label: 'Customer' },
    { key: 'service', label: 'Service' },
    { key: 'date', label: 'Tanggal' },
    { key: 'planner', label: 'Planner' },
    { key: 'action', label: '' },
];
</script>

<template>
    <Head title="Planner" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall
                        title="Planner"
                        description="Daftar transaksi yang sudah success dan ticket belum ditutup."
                    />
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <div class="grid gap-4 md:grid-cols-12">
                        <div class="md:col-span-6">
                            <Label>Cari</Label>
                            <Input v-model="search" placeholder="ticket / nama / email / phone" class="rounded-xl" />
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

                    <template #cell-planner="{ item }">
                        <span class="rounded-full border border-sidebar-border/70 bg-muted/30 px-2 py-0.5 text-xs">
                            {{ item.planner_count }} item
                        </span>
                    </template>

                    <template #cell-action="{ item }">
                        <Button as-child size="sm" class="rounded-xl">
                            <Link :href="route('transaction.planner.edit', { transaction: item.id })">
                                Buka
                            </Link>
                        </Button>
                    </template>
                </PaginatedTable>
            </div>
        </div>
    </AppLayout>
</template>
