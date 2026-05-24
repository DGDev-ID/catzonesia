<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    transaction: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Transaction', href: '/transaction' },
    { title: `Detail #${props.transaction.id}`, href: `/transaction/${props.transaction.id}` },
];

const statusClass = (status: string) => {
    if (status === 'success') return 'bg-green-100 text-green-700';
    if (status === 'failed') return 'bg-red-100 text-red-700';
    return 'bg-yellow-100 text-yellow-700';
};
</script>

<template>
    <Head title="Transaction Detail" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" :title="`Detail Transaksi #${props.transaction.id}`" description="Informasi lengkap transaksi." />
                    <Link href="/transaction" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ? Kembali
                    </Link>
                </div>

                <!-- Transaction Info -->
                <div class="rounded-2xl border bg-background shadow-sm p-8">
                    <h3 class="mb-6 text-base font-semibold">Informasi Transaksi</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Email Customer</p>
                            <p class="text-sm font-medium">{{ props.transaction.cust_email }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Total</p>
                            <p class="text-sm font-medium">{{ props.transaction.total_price }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Fee</p>
                            <p class="text-sm font-medium">{{ props.transaction.fee }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Harga</p>
                            <p class="text-sm font-medium">{{ props.transaction.price }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Status</p>
                            <span :class="statusClass(props.transaction.status)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize">
                                {{ props.transaction.status }}
                            </span>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Tanggal</p>
                            <p class="text-sm font-medium">{{ props.transaction.created_at }}</p>
                        </div>
                    </div>
                </div>

                <!-- Grooming Info -->
                <div v-if="props.transaction.transaction_grooming" class="rounded-2xl border bg-background shadow-sm p-8">
                    <h3 class="mb-6 text-base font-semibold">Informasi Grooming</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Nama Pemilik</p>
                            <p class="text-sm font-medium">{{ props.transaction.transaction_grooming.owner_name }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">No. Telp Pemilik</p>
                            <p class="text-sm font-medium">{{ props.transaction.transaction_grooming.owner_phone }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Nama Kucing</p>
                            <p class="text-sm font-medium">{{ props.transaction.transaction_grooming.cat_name }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Usia Kucing</p>
                            <p class="text-sm font-medium">{{ props.transaction.transaction_grooming.cat_age }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Berat Perkiraan</p>
                            <p class="text-sm font-medium">{{ props.transaction.transaction_grooming.cat_estimated_weight }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium text-muted-foreground uppercase tracking-wide">Catatan</p>
                            <p class="text-sm font-medium">{{ props.transaction.transaction_grooming.notes }}</p>
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold">Detail Item</h3>
                    </div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-muted/50">
                            <tr class="text-muted-foreground">
                                <th scope="col" class="px-6 py-4 text-left font-medium">No</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Produk / Paket</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Jumlah</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Unit</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Harga</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in transaction.transaction_details" :key="item.id" class="border-t transition hover:bg-muted/40">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium">{{ item.product?.name || item.package?.name }}</td>
                                <td class="px-6 py-4">{{ item.amount }}</td>
                                <td class="px-6 py-4">{{ item.unit?.name }}</td>
                                <td class="px-6 py-4">{{ item.price }}</td>
                                <td class="px-6 py-4 text-muted-foreground">{{ item.notes }}</td>
                            </tr>
                            <tr v-if="!transaction.transaction_details?.length">
                                <td colspan="6" class="px-6 py-10 text-center text-muted-foreground">Tidak ada item.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Transaction Logs -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold">Log Transaksi</h3>
                    </div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-muted/50">
                            <tr class="text-muted-foreground">
                                <th scope="col" class="px-6 py-4 text-left font-medium">No</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Status</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Catatan</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in transaction.transaction_logs" :key="item.id" class="border-t transition hover:bg-muted/40">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span :class="statusClass(item.status)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-muted-foreground">{{ item.notes }}</td>
                                <td class="px-6 py-4 text-muted-foreground">{{ item.created_at }}</td>
                            </tr>
                            <tr v-if="!transaction.transaction_logs?.length">
                                <td colspan="4" class="px-6 py-10 text-center text-muted-foreground">Tidak ada log.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AppLayout>
</template>