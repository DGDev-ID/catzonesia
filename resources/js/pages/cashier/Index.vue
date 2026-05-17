<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    products: any;
    packages: any;
    todayTransactions: any;
}>();

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
        cartItems.value.push({
            ...item,
            type,
            amount: 1,
        });
    }
};

const removeFromCart = (item: any) => {
    const index = cartItems.value.findIndex((i) => (item.type === 'product' ? i.product_id === item.id : i.package_id === item.id));

    if (index !== -1) {
        cartItems.value.splice(index, 1);
    }
};

const updateQuantity = (item: any, delta: number) => {
    const index = cartItems.value.findIndex((i) => (item.type === 'product' ? i.product_id === item.id : i.package_id === item.id));

    if (index !== -1) {
        cartItems.value[index].amount += delta;

        if (cartItems.value[index].amount <= 0) {
            cartItems.value.splice(index, 1);
        }
    }
};

const getTotalPrice = () => {
    return cartItems.value.reduce((total, item) => {
        const price = parseFloat(item.price.toString());
        const amount = parseInt(item.amount.toString());
        return total + price * amount;
    }, 0);
};

const hasGroomingPackage = () => {
    return cartItems.value.some((item) => item.type === 'package' && item.is_grooming);
};

const checkout = () => {
    const payload = {
        cust_email: customerEmail.value,
        items: cartItems.value.map((item) => ({
            type: item.type,
            id: item.id,
            amount: item.amount,
        })),
        grooming_info: hasGroomingPackage() ? groomingInfo.value : null,
    };

    router.post('/cashier/checkout', payload, {
        onSuccess: () => {
            cartItems.value = [];
            customerEmail.value = '';
            groomingInfo.value = {
                owner_name: '',
                owner_phone: '',
                cat_name: '',
                cat_age: '',
                cat_estimated_weight: '',
                notes: '',
            };
        },
    });
};

const markAsSuccess = (transactionId: number) => {
    router.post(`/cashier/transaction/${transactionId}/success`);
};

const markAsFailed = (transactionId: number) => {
    router.post(`/cashier/transaction/${transactionId}/failed`);
};
</script>

<template>
    <Head title="Cashier" />

    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="mb-8 text-2xl font-bold">Cashier</h1>

            <!-- Today's Transactions -->
            <div class="mb-8">
                <h2 class="mb-4 text-xl font-semibold">Today's Transactions</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-muted/50">
                            <tr class="text-muted-foreground">
                                <th scope="col" class="px-6 py-4 text-left font-medium">No</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Customer Email</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Total Price</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Status</th>
                                <th scope="col" class="px-6 py-4 text-left font-medium">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(transaction, index) in todayTransactions" :key="transaction.id" class="border-t transition hover:bg-muted/40">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4">{{ transaction.cust_email }}</td>
                                <td class="px-6 py-4">{{ transaction.total_price }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="
                                            transaction.status === 'success'
                                                ? 'text-green-600'
                                                : transaction.status === 'failed'
                                                  ? 'text-red-600'
                                                  : 'text-yellow-600'
                                        "
                                    >
                                        {{ transaction.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button
                                        v-if="transaction.status === 'pending'"
                                        type="button"
                                        @click="markAsSuccess(transaction.id)"
                                        class="mr-2 rounded-md bg-green-600 px-4 py-2 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                                    >
                                        Success
                                    </button>

                                    <button
                                        v-if="transaction.status === 'pending'"
                                        type="button"
                                        @click="markAsFailed(transaction.id)"
                                        class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                    >
                                        Failed
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex gap-8">
                <!-- Products and Packages -->
                <div class="flex-1">
                    <h2 class="mb-4 text-xl font-semibold">Products & Packages</h2>

                    <!-- Products -->
                    <div class="mb-8">
                        <h3 class="mb-4 text-lg font-medium">Products</h3>

                        <div class="grid grid-cols-3 gap-4">
                            <div v-for="product in products" :key="product.id" class="rounded-md border p-4 transition hover:shadow-md">
                                <div class="mb-2 flex items-start justify-between">
                                    <div class="font-medium">{{ product.name }}</div>
                                    <div class="text-sm text-gray-500">{{ product.price }}</div>
                                </div>

                                <div class="mb-4 text-sm text-gray-500">{{ product.sku }}</div>

                                <button
                                    type="button"
                                    @click="addToCart(product, 'product')"
                                    class="w-full rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Packages -->
                    <div>
                        <h3 class="mb-4 text-lg font-medium">Packages</h3>

                        <div class="grid grid-cols-3 gap-4">
                            <div v-for="packageItem in packages" :key="packageItem.id" class="rounded-md border p-4 transition hover:shadow-md">
                                <div class="mb-2 flex items-start justify-between">
                                    <div class="font-medium">{{ packageItem.name }}</div>
                                    <div class="text-sm text-gray-500">{{ packageItem.price }}</div>
                                </div>

                                <div class="mb-4 text-sm text-gray-500">{{ packageItem.description }}</div>

                                <button
                                    type="button"
                                    @click="addToCart(packageItem, 'package')"
                                    class="w-full rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart -->
                <div class="w-96">
                    <h2 class="mb-4 text-xl font-semibold">Cart</h2>

                    <div class="mb-4 rounded-md border p-4">
                        <div v-if="cartItems.length === 0" class="py-8 text-center text-gray-500">Your cart is empty.</div>

                        <div v-else>
                            <div v-for="item in cartItems" :key="item.id" class="mb-4 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="font-medium">{{ item.name }}</div>
                                    <div class="text-sm text-gray-500">{{ item.type === 'product' ? 'Product' : 'Package' }}</div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="updateQuantity(item, -1)"
                                        class="flex h-8 w-8 items-center justify-center rounded-md bg-gray-300 hover:bg-gray-400 focus:outline-none"
                                    >
                                        -
                                    </button>

                                    <div class="w-8 text-center">{{ item.amount }}</div>

                                    <button
                                        type="button"
                                        @click="updateQuantity(item, 1)"
                                        class="flex h-8 w-8 items-center justify-center rounded-md bg-gray-300 hover:bg-gray-400 focus:outline-none"
                                    >
                                        +
                                    </button>

                                    <button
                                        type="button"
                                        @click="removeFromCart(item)"
                                        class="flex h-8 w-8 items-center justify-center rounded-md bg-red-500 text-white hover:bg-red-600 focus:outline-none"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t py-4 font-medium">
                                <div>Total Price:</div>
                                <div>{{ getTotalPrice().toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }) }}</div>
                            </div>

                            <div class="mb-4">
                                <label for="customer_email" class="mb-1 block text-sm font-medium text-gray-700">Customer Email</label>
                                <input
                                    id="customer_email"
                                    v-model="customerEmail"
                                    type="email"
                                    placeholder="Enter customer email"
                                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>

                            <div v-if="hasGroomingPackage()" class="mb-4">
                                <h3 class="mb-4 text-lg font-medium">Grooming Information</h3>

                                <div class="mb-4">
                                    <label for="owner_name" class="mb-1 block text-sm font-medium text-gray-700">Owner Name</label>
                                    <input
                                        id="owner_name"
                                        v-model="groomingInfo.owner_name"
                                        type="text"
                                        placeholder="Enter owner name"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div class="mb-4">
                                    <label for="owner_phone" class="mb-1 block text-sm font-medium text-gray-700">Owner Phone</label>
                                    <input
                                        id="owner_phone"
                                        v-model="groomingInfo.owner_phone"
                                        type="text"
                                        placeholder="Enter owner phone"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div class="mb-4">
                                    <label for="cat_name" class="mb-1 block text-sm font-medium text-gray-700">Cat Name</label>
                                    <input
                                        id="cat_name"
                                        v-model="groomingInfo.cat_name"
                                        type="text"
                                        placeholder="Enter cat name"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div class="mb-4">
                                    <label for="cat_age" class="mb-1 block text-sm font-medium text-gray-700">Cat Age</label>
                                    <input
                                        id="cat_age"
                                        v-model="groomingInfo.cat_age"
                                        type="number"
                                        placeholder="Enter cat age"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div class="mb-4">
                                    <label for="cat_estimated_weight" class="mb-1 block text-sm font-medium text-gray-700"
                                        >Cat Estimated Weight</label
                                    >
                                    <input
                                        id="cat_estimated_weight"
                                        v-model="groomingInfo.cat_estimated_weight"
                                        type="number"
                                        step="0.01"
                                        placeholder="Enter cat estimated weight"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                </div>

                                <div class="mb-4">
                                    <label for="grooming_notes" class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea
                                        id="grooming_notes"
                                        v-model="groomingInfo.notes"
                                        placeholder="Enter notes"
                                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    ></textarea>
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="checkout"
                                class="w-full rounded-md bg-green-600 px-4 py-2 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            >
                                Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
