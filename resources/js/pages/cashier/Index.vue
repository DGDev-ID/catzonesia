<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    products: any;
    packages: any;
    todayTransactions: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Kasir', href: '/cashier' },
];

const cartItems = ref<any[]>([]);
const customerEmail = ref('');
const groomingInfo = ref<any>({
    owner_name: '',
    owner_phone: '',
    cat_name: '',
    cat_age: '',
    cat_estimated_weight: '',
    notes: '',
});

const addToCart = (item: any, type: 'product' | 'package') => {
    const existing = cartItems.value.find((i) => (type === 'product' ? i.product_id === item.id : i.package_id === item.id));
    if (existing) {
        existing.amount++;
    } else {
        cartItems.value.push({ ...item, type, amount: 1 });
    }
};

const removeFromCart = (item: any) => {
    const index = cartItems.value.findIndex((i) => (item.type === 'product' ? i.product_id === item.id : i.package_id === item.id));
    if (index !== -1) cartItems.value.splice(index, 1);
};

const updateQuantity = (item: any, delta: number) => {
    const index = cartItems.value.findIndex((i) => (item.type === 'product' ? i.product_id === item.id : i.package_id === item.id));
    if (index !== -1) {
        cartItems.value[index].amount += delta;
        if (cartItems.value[index].amount <= 0) cartItems.value.splice(index, 1);
    }
};

const getTotalPrice = () => {
    return cartItems.value.reduce((total, item) => {
        return total + parseFloat(item.price.toString()) * parseInt(item.amount.toString());
    }, 0);
};

const hasGroomingPackage = () => {
    return cartItems.value.some((item) => item.type === 'package' && item.is_grooming);
};

const checkout = () => {
    const payload = {
        cust_email: customerEmail.value,
        items: cartItems.value.map((item) => ({ type: item.type, id: item.id, amount: item.amount })),
        grooming_info: hasGroomingPackage() ? groomingInfo.value : null,
    };
    router.post('/cashier/checkout', payload, {
        onSuccess: () => {
            cartItems.value = [];
            customerEmail.value = '';
            groomingInfo.value = { owner_name: '', owner_phone: '', cat_name: '', cat_age: '', cat_estimated_weight: '', notes: '' };
        },
    });
};

const markAsSuccess = (transactionId: number) => {
    router.post(`/cashier/transaction/${transactionId}/success`);
};

const markAsFailed = (transactionId: number) => {
    router.post(`/cashier/transaction/${transactionId}/failed`);
};

const statusClass = (status: string) => {
    if (status === 'success') return 'bg-green-100 text-green-700';
    if (status === 'failed') return 'bg-red-100 text-red-700';
    return 'bg-yellow-100 text-yellow-700';
};
</script>

<template>
    <Head title="Kasir" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <Heading variant="small" title="Kasir" description="Tambah transaksi & kelola pembayaran hari ini." />

                <!-- Today's Transactions -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold">Transaksi Hari Ini</h3>
                    </div>
                    <table class="min-w-full text-sm">
                        <thead class="bg-muted/50">
                            <tr class="text-muted-foreground">
                                <th scope="col" class="px-6 py-4 text-left font-medium">No</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Email Customer</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Total</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Status</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(transaction, index) in todayTransactions" :key="transaction.id" class="border-t transition hover:bg-muted/40">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4">{{ transaction.cust_email }}</td>
                                <td class="px-6 py-4">{{ transaction.total_price }}</td>
                                <td class="px-6 py-4">
                                    <span :class="statusClass(transaction.status)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize">
                                        {{ transaction.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button
                                            v-if="transaction.status === 'pending'"
                                            type="button"
                                            @click="markAsSuccess(transaction.id)"
                                            class="inline-flex items-center justify-center rounded-lg bg-green-100 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-500 hover:text-white transition"
                                        >
                                            Sukses
                                        </button>
                                        <button
                                            v-if="transaction.status === 'pending'"
                                            type="button"
                                            @click="markAsFailed(transaction.id)"
                                            class="inline-flex items-center justify-center rounded-lg bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-500 hover:text-white transition"
                                        >
                                            Gagal
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!todayTransactions?.length">
                                <td colspan="5" class="px-6 py-10 text-center text-muted-foreground">Belum ada transaksi hari ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Main Content: Products + Cart -->
                <div class="flex gap-8 items-start">

                    <!-- Products & Packages -->
                    <div class="flex-1 space-y-6">

                        <!-- Products -->
                        <div class="rounded-2xl border bg-background shadow-sm p-6">
                            <h3 class="mb-4 text-base font-semibold">Produk</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div
                                    v-for="product in products"
                                    :key="product.id"
                                    class="rounded-xl border bg-muted/30 p-4 transition hover:shadow-md"
                                >
                                    <div class="mb-1 font-medium text-sm">{{ product.name }}</div>
                                    <div class="mb-1 text-xs text-muted-foreground">{{ product.sku }}</div>
                                    <div class="mb-3 text-sm font-semibold text-primary">{{ product.price }}</div>
                                    <button
                                        type="button"
                                        @click="addToCart(product, 'product')"
                                        class="w-full inline-flex items-center justify-center rounded-xl bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                                    >
                                        + Keranjang
                                    </button>
                                </div>
                                <div v-if="!products?.length" class="col-span-3 py-8 text-center text-sm text-muted-foreground">Tidak ada produk.</div>
                            </div>
                        </div>

                        <!-- Packages -->
                        <div class="rounded-2xl border bg-background shadow-sm p-6">
                            <h3 class="mb-4 text-base font-semibold">Paket</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div
                                    v-for="packageItem in packages"
                                    :key="packageItem.id"
                                    class="rounded-xl border bg-muted/30 p-4 transition hover:shadow-md"
                                >
                                    <div class="mb-1 font-medium text-sm">{{ packageItem.name }}</div>
                                    <div class="mb-1 text-xs text-muted-foreground">{{ packageItem.description }}</div>
                                    <div class="mb-1 text-xs">
                                        <span v-if="packageItem.is_grooming" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">Grooming</span>
                                    </div>
                                    <div class="mb-3 text-sm font-semibold text-primary">{{ packageItem.price }}</div>
                                    <button
                                        type="button"
                                        @click="addToCart(packageItem, 'package')"
                                        class="w-full inline-flex items-center justify-center rounded-xl bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                                    >
                                        + Keranjang
                                    </button>
                                </div>
                                <div v-if="!packages?.length" class="col-span-3 py-8 text-center text-sm text-muted-foreground">Tidak ada paket.</div>
                            </div>
                        </div>

                    </div>

                    <!-- Cart Sidebar -->
                    <div class="w-96 shrink-0">
                        <div class="rounded-2xl border bg-background shadow-sm p-6 sticky top-6 space-y-4">
                            <h3 class="text-base font-semibold">Keranjang</h3>

                            <div v-if="cartItems.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                                Keranjang kosong.
                            </div>

                            <div v-else class="space-y-4">
                                <!-- Cart Items -->
                                <div class="space-y-3">
                                    <div v-for="item in cartItems" :key="item.id" class="flex items-center justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium">{{ item.name }}</p>
                                            <p class="text-xs text-muted-foreground">{{ item.type === 'product' ? 'Produk' : 'Paket' }}</p>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <button type="button" @click="updateQuantity(item, -1)" class="flex h-6 w-6 items-center justify-center rounded-md bg-muted text-sm font-bold hover:bg-muted/80 transition">−</button>
                                            <span class="w-6 text-center text-sm">{{ item.amount }}</span>
                                            <button type="button" @click="updateQuantity(item, 1)" class="flex h-6 w-6 items-center justify-center rounded-md bg-muted text-sm font-bold hover:bg-muted/80 transition">+</button>
                                            <button type="button" @click="removeFromCart(item)" class="flex h-6 w-6 items-center justify-center rounded-md bg-red-100 text-red-600 hover:bg-red-500 hover:text-white transition text-sm font-bold">×</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="flex items-center justify-between border-t pt-3 font-semibold">
                                    <span class="text-sm">Total</span>
                                    <span class="text-sm">{{ getTotalPrice().toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }) }}</span>
                                </div>

                                <!-- Customer Email -->
                                <div>
                                    <label for="customer_email" class="mb-1 block text-sm font-medium">Email Customer</label>
                                    <input
                                        id="customer_email"
                                        v-model="customerEmail"
                                        type="email"
                                        placeholder="contoh@email.com"
                                        class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                    />
                                </div>

                                <!-- Grooming Info -->
                                <div v-if="hasGroomingPackage()" class="rounded-xl border bg-muted/30 p-4 space-y-3">
                                    <h4 class="text-sm font-semibold">Informasi Grooming</h4>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium">Nama Pemilik</label>
                                        <input v-model="groomingInfo.owner_name" type="text" placeholder="Nama pemilik" class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium">No. Telp Pemilik</label>
                                        <input v-model="groomingInfo.owner_phone" type="text" placeholder="08xx" class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium">Nama Kucing</label>
                                        <input v-model="groomingInfo.cat_name" type="text" placeholder="Nama kucing" class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium">Usia Kucing</label>
                                        <input v-model="groomingInfo.cat_age" type="number" placeholder="Usia (tahun)" class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium">Berat Perkiraan (kg)</label>
                                        <input v-model="groomingInfo.cat_estimated_weight" type="number" step="0.01" placeholder="0.00" class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium">Catatan</label>
                                        <textarea v-model="groomingInfo.notes" placeholder="Catatan tambahan" rows="2" class="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"></textarea>
                                    </div>
                                </div>

                                <!-- Checkout Button -->
                                <button
                                    type="button"
                                    @click="checkout"
                                    class="w-full inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                                >
                                    Checkout
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
