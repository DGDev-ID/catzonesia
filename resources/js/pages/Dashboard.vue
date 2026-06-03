<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { AlertTriangle, BadgeDollarSign, BarChart3, Box, CheckCircle2, Clock, TrendingUp, Users, XCircle } from 'lucide-vue-next';
import { formatCurrency } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

const props = defineProps<{
    stats: {
        total_products: number;
        total_users: number;
        transactions_this_month: number;
        revenue_this_month: number;
    };
    transactionStatus: {
        pending: number;
        success: number;
        failed: number;
    };
    recentTransactions: {
        id: number;
        cust_email: string;
        total_price: number;
        status: string;
        created_at: string;
    }[];
    lowStockProducts: {
        id: number;
        name: string;
        sku: string;
        stock: number;
        stock_alert: number;
        base_unit?: { name: string };
    }[];
}>();

const formatDate = (date: string) =>
    new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(date));

const statusConfig: Record<string, { label: string; class: string; icon: any }> = {
    pending: { label: 'Pending',  class: 'bg-amber-100 text-amber-700',  icon: Clock },
    success: { label: 'Sukses',   class: 'bg-green-100 text-green-700',  icon: CheckCircle2 },
    failed:  { label: 'Gagal',    class: 'bg-red-100 text-red-600',      icon: XCircle },
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-6">

            <!-- Stat Cards -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <!-- Total Products -->
                <div class="flex items-center gap-4 rounded-2xl border bg-background p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <Box :size="22" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Total Produk</p>
                        <p class="text-2xl font-bold">{{ stats.total_products }}</p>
                    </div>
                </div>

                <!-- Total Users -->
                <div class="flex items-center gap-4 rounded-2xl border bg-background p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600">
                        <Users :size="22" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Total User</p>
                        <p class="text-2xl font-bold">{{ stats.total_users }}</p>
                    </div>
                </div>

                <!-- Transactions this month -->
                <div class="flex items-center gap-4 rounded-2xl border bg-background p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <TrendingUp :size="22" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Transaksi Bulan Ini</p>
                        <p class="text-2xl font-bold">{{ stats.transactions_this_month }}</p>
                    </div>
                </div>

                <!-- Revenue this month -->
                <div class="flex items-center gap-4 rounded-2xl border bg-background p-5 shadow-sm">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-600">
                        <BadgeDollarSign :size="22" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Pendapatan Bulan Ini</p>
                        <p class="text-xl font-bold">{{ formatCurrency(stats.revenue_this_month) }}</p>
                    </div>
                </div>
            </div>

            <!-- Main content grid -->
            <div class="grid gap-6 lg:grid-cols-3">

                <!-- Recent Transactions (2/3) -->
                <div class="lg:col-span-2 rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between border-b px-5 py-4">
                        <h2 class="text-sm font-semibold">Transaksi Terbaru</h2>
                        <a href="/transaction" class="text-xs text-primary hover:underline">Lihat semua →</a>
                    </div>
                    <div v-if="recentTransactions.length" class="divide-y">
                        <div
                            v-for="trx in recentTransactions"
                            :key="trx.id"
                            class="flex items-center justify-between px-5 py-3.5"
                        >
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium">{{ trx.cust_email || '—' }}</p>
                                <p class="text-xs text-muted-foreground">{{ formatDate(trx.created_at) }}</p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0 ml-4">
                                <span class="text-sm font-semibold">{{ formatCurrency(trx.total_price) }}</span>
                                <span
                                    :class="statusConfig[trx.status]?.class ?? 'bg-gray-100 text-gray-600'"
                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium"
                                >
                                    <component :is="statusConfig[trx.status]?.icon" :size="11" />
                                    {{ statusConfig[trx.status]?.label ?? trx.status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex items-center justify-center py-12 text-sm text-muted-foreground">
                        Belum ada transaksi
                    </div>
                </div>

                <!-- Right column -->
                <div class="space-y-6">

                    <!-- Transaction Status -->
                    <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                        <div class="border-b px-5 py-4">
                            <h2 class="text-sm font-semibold">Status Transaksi</h2>
                            <p class="text-xs text-muted-foreground">Keseluruhan</p>
                        </div>
                        <div class="space-y-3 p-5">
                            <!-- Success -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <CheckCircle2 :size="16" class="text-green-600" />
                                    <span class="text-sm">Sukses</span>
                                </div>
                                <span class="text-sm font-semibold">{{ transactionStatus.success }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-muted overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-green-500 transition-all"
                                    :style="{ width: (transactionStatus.success / Math.max(transactionStatus.success + transactionStatus.pending + transactionStatus.failed, 1) * 100) + '%' }"
                                />
                            </div>

                            <!-- Pending -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Clock :size="16" class="text-amber-500" />
                                    <span class="text-sm">Pending</span>
                                </div>
                                <span class="text-sm font-semibold">{{ transactionStatus.pending }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-muted overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-amber-400 transition-all"
                                    :style="{ width: (transactionStatus.pending / Math.max(transactionStatus.success + transactionStatus.pending + transactionStatus.failed, 1) * 100) + '%' }"
                                />
                            </div>

                            <!-- Failed -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <XCircle :size="16" class="text-red-500" />
                                    <span class="text-sm">Gagal</span>
                                </div>
                                <span class="text-sm font-semibold">{{ transactionStatus.failed }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-muted overflow-hidden">
                                <div
                                    class="h-full rounded-full bg-red-500 transition-all"
                                    :style="{ width: (transactionStatus.failed / Math.max(transactionStatus.success + transactionStatus.pending + transactionStatus.failed, 1) * 100) + '%' }"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2 border-b px-5 py-4">
                            <AlertTriangle :size="15" class="text-amber-500" />
                            <h2 class="text-sm font-semibold">Stok Menipis</h2>
                        </div>
                        <div v-if="lowStockProducts.length" class="divide-y">
                            <div
                                v-for="product in lowStockProducts"
                                :key="product.id"
                                class="flex items-center justify-between px-5 py-3"
                            >
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium">{{ product.name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ product.sku }}</p>
                                </div>
                                <div class="ml-3 shrink-0 text-right">
                                    <p class="text-sm font-semibold text-red-600">{{ product.stock }} {{ product.base_unit?.name ?? '' }}</p>
                                    <p class="text-xs text-muted-foreground">Min: {{ product.stock_alert }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="flex items-center justify-center py-8 text-sm text-muted-foreground">
                            Semua stok aman
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </AppLayout>
</template>

