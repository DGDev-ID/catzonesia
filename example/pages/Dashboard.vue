<script setup lang="ts">
import RecentTransactions from '@/components/dashboard/RecentTransactions.vue';
import RevenueChart from '@/components/dashboard/RevenueChart.vue';
import StatCard from '@/components/dashboard/StatCard.vue';
import StatusDonutChart from '@/components/dashboard/StatusDonutChart.vue';
import TopPackagesChart from '@/components/dashboard/TopPackagesChart.vue';
import type { SubStat } from '@/components/dashboard/StatCard.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Briefcase, Receipt, RefreshCw, TrendingUp, Wrench } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

const props = defineProps<{
    stats: {
        totalTransactions: number;
        successTransactions: number;
        pendingTransactions: number;
        failedTransactions: number;
        totalServices: number;
        totalServicePackages: number;
        totalServiceCategories: number;
        totalPortfolios: number;
        thisMonthTransactions: number;
        thisMonthSuccess: number;
        thisMonthPending: number;
        thisMonthFailed: number;
        lastMonthTransactions: number;
        revenueThisMonth: number;
        revenueLastMonth: number;
        successRevenue: number;
        pendingRevenue: number;
        failedRevenue: number;
    };
    chartData: {
        month: string;
        total_transactions: number;
        success_count: number;
        pending_count: number;
        revenue: number;
    }[];
    recentTransactions: {
        id: number;
        ticket_id: string;
        cust_name: string;
        cust_email: string;
        total_price: string | number;
        start_date: string;
        last_status: 'pending' | 'success' | 'failed' | null;
        package_title: string | null;
        service_title: string | null;
    }[];
    topPackages: {
        id: number;
        package_title: string;
        service_title: string;
        transaction_count: number;
    }[];
    selectedMonths: number;
}>();

// ── Refresh state ──
const isRefreshing = ref(false);
const lastRefreshed = ref<Date>(new Date());

function refresh() {
    if (isRefreshing.value) return;
    isRefreshing.value = true;
    router.reload({
        only: ['stats', 'chartData', 'recentTransactions', 'topPackages'],
        onFinish: () => {
            isRefreshing.value = false;
            lastRefreshed.value = new Date();
        },
    });
}

let refreshInterval: number | null = null;

onMounted(() => {
    refreshInterval = window.setInterval(() => {
        refresh();
    }, 30000); // 30 detik
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

// ── Period filter ──
function setMonths(months: number) {
    router.get(
        route('dashboard'),
        { months },
        {
            preserveScroll: true,
            preserveState: false,
        },
    );
}

const monthOptions = [
    { label: '3 Bulan', value: 3 },
    { label: '6 Bulan', value: 6 },
    { label: '12 Bulan', value: 12 },
];

const formatLastRefreshed = computed(() =>
    lastRefreshed.value.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
);

// ── Currency ──
const formatCurrency = (value: number) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);

const formatCompact = (value: number) => {
    if (value >= 1_000_000) return `Rp ${(value / 1_000_000).toFixed(1)} jt`;
    if (value >= 1_000) return `Rp ${(value / 1_000).toFixed(0)} rb`;
    return formatCurrency(value);
};

const percentChange = (current: number, previous: number): number | null => {
    if (previous === 0 && current === 0) return null;
    if (previous === 0) return 100;
    return ((current - previous) / previous) * 100;
};

const txMonthChange = computed(() => percentChange(props.stats.thisMonthTransactions, props.stats.lastMonthTransactions));
const revenueMonthChange = computed(() => percentChange(props.stats.revenueThisMonth, props.stats.revenueLastMonth));

// Card 1: Total Transaksi
const card1SubStats = computed<SubStat[]>(() => [
    { label: 'Success', value: props.stats.successTransactions, color: 'text-emerald-500' },
    { label: 'Pending', value: props.stats.pendingTransactions, color: 'text-amber-500' },
    { label: 'Failed', value: props.stats.failedTransactions, color: 'text-red-500' },
]);

// Card 2: Total Layanan
const card2SubStats = computed<SubStat[]>(() => [
    { label: 'Layanan', value: props.stats.totalServices },
    { label: 'Paket', value: props.stats.totalServicePackages },
    { label: 'Kategori', value: props.stats.totalServiceCategories },
]);

// Card 3: Transaksi Bulan Ini
const card3SubStats = computed<SubStat[]>(() => [
    { label: 'Paid', value: props.stats.thisMonthSuccess, color: 'text-emerald-500' },
    { label: 'Pending', value: props.stats.thisMonthPending, color: 'text-amber-500' },
    { label: 'Failed', value: props.stats.thisMonthFailed, color: 'text-red-500' },
]);

// Card 4: Pendapatan Bulan Ini
const card4SubStats = computed<SubStat[]>(() => [
    { label: 'Success', value: formatCompact(props.stats.successRevenue), color: 'text-emerald-500' },
    { label: 'Pending', value: formatCompact(props.stats.pendingRevenue), color: 'text-amber-500' },
    { label: 'Failed', value: formatCompact(props.stats.failedRevenue), color: 'text-red-500' },
]);

// Donut chart data
const donutData = computed(() => [
    { label: 'Success', value: props.stats.successTransactions, color: '#10b981' },
    { label: 'Pending', value: props.stats.pendingTransactions, color: '#f59e0b' },
    { label: 'Failed', value: props.stats.failedTransactions, color: '#ef4444' },
]);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-5 p-4 md:p-6">

            <!-- Page Header with Refresh & Period Filter -->
            <div class="dashboard-enter flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" style="--delay: 0">
                <div>
                    <h1 class="text-lg font-semibold">Dashboard</h1>
                    <p class="text-xs text-muted-foreground">
                        Terakhir diperbarui: {{ formatLastRefreshed }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Period filter buttons -->
                    <div class="flex overflow-hidden rounded-lg border bg-muted/30">
                        <button
                            v-for="opt in monthOptions"
                            :key="opt.value"
                            :class="[
                                'px-3 py-1.5 text-xs font-medium transition-colors',
                                selectedMonths === opt.value
                                    ? 'bg-primary text-primary-foreground'
                                    : 'text-muted-foreground hover:bg-muted hover:text-foreground',
                            ]"
                            @click="setMonths(opt.value)"
                        >
                            {{ opt.label }}
                        </button>
                    </div>

                    <!-- Auto Refresh Indicator -->
                    <!-- <div
                        class="flex items-center gap-1.5 rounded-lg border bg-card px-3 py-1.5 text-xs font-medium shadow-sm transition-all"
                        :class="isRefreshing ? 'text-primary' : 'text-muted-foreground'"
                    >
                        <RefreshCw
                            :class="['h-3.5 w-3.5', isRefreshing ? 'animate-spin' : '']"
                        />
                        <span>{{ isRefreshing ? 'Memuat...' : 'Auto-refresh aktif' }}</span>
                    </div> -->
                </div>
            </div>

            <!-- KPI Cards Row -->
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="dashboard-enter" style="--delay: 1">
                    <StatCard
                        title="Total Transaksi"
                        :value="String(stats.totalTransactions)"
                        :icon="Receipt"
                        icon-bg-class="bg-indigo-500/15 text-indigo-500"
                        :sub-stats="card1SubStats"
                    />
                </div>

                <div class="dashboard-enter" style="--delay: 2">
                    <StatCard
                        title="Total Layanan"
                        :value="String(stats.totalServices)"
                        :icon="Wrench"
                        icon-bg-class="bg-blue-500/15 text-blue-500"
                        :sub-stats="card2SubStats"
                    />
                </div>

                <div class="dashboard-enter" style="--delay: 3">
                    <StatCard
                        title="Transaksi Bulan Ini"
                        :value="String(stats.thisMonthTransactions)"
                        :icon="TrendingUp"
                        icon-bg-class="bg-emerald-500/15 text-emerald-500"
                        :change="txMonthChange"
                        :change-label="txMonthChange === null ? '0%' : undefined"
                        :sub-stats="card3SubStats"
                    />
                </div>

                <div class="dashboard-enter" style="--delay: 4">
                    <StatCard
                        title="Pendapatan Bulan Ini"
                        :value="formatCurrency(stats.revenueThisMonth)"
                        :icon="Briefcase"
                        icon-bg-class="bg-amber-500/15 text-amber-500"
                        :change="revenueMonthChange"
                        :change-label="revenueMonthChange === null ? '0%' : undefined"
                        :sub-stats="card4SubStats"
                    />
                </div>
            </div>

            <!-- Chart (full width) -->
            <div class="dashboard-enter" style="--delay: 5">
                <RevenueChart :data="chartData" :months="selectedMonths" />
            </div>

            <!-- Bottom row: Donut + Top Packages -->
            <div class="grid gap-4 md:grid-cols-2">
                <div class="dashboard-enter" style="--delay: 6">
                    <StatusDonutChart :data="donutData" />
                </div>

                <div class="dashboard-enter" style="--delay: 7">
                    <TopPackagesChart :data="topPackages" />
                </div>
            </div>

            <!-- Recent Transactions (full width) -->
            <div class="dashboard-enter" style="--delay: 8">
                <RecentTransactions :transactions="recentTransactions" />
            </div>

        </div>
    </AppLayout>
</template>

<style scoped>
.dashboard-enter {
    animation: dashboardSlideUp 0.45s ease-out both;
    animation-delay: calc(var(--delay, 0) * 70ms);
}

@keyframes dashboardSlideUp {
    from {
        opacity: 0;
        transform: translateY(14px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
