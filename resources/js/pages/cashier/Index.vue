<script setup lang="ts">
    import PaginatedTable from '@/components/custom/PaginatedTable.vue';
    import Heading from '@/components/Heading.vue';
    import AppLayout from '@/layouts/AppLayout.vue';
    import {
        type BreadcrumbItem
    } from '@/types';
    import {
        Head,
        Link,
        router
    } from '@inertiajs/vue3';
    import {
        computed,
        ref,
        watch
    } from 'vue';
    import {
        Check,
        Eye,
        Search,
        ShoppingCart,
        X
    } from 'lucide-vue-next';

    type CatalogItem = {
        id: number;
        type: 'product' | 'package';
        name: string;
        price: number;
        img_url: string | null;
        sku ? : string | null;
        is_grooming ? : boolean;
        description ? : string | null;
    };

    type CartItem = CatalogItem & {
        quantity: number;
    };

    const props = defineProps < {
        filter: string;
        search: string;
        categories: any[];
        catalog: any;
        pendingTransactions: any;
        successTransactions: any;
    } > ();

    const breadcrumbs: BreadcrumbItem[] = [{
        title: 'Kasir',
        href: '/cashier'
    }];

    const selectedFilter = ref < string > (props.filter ?? 'all');
    const searchTerm = ref < string > (props.search ?? '');

    watch(
        () => props.filter,
        (value) => {
            selectedFilter.value = value ?? 'all';
        },
    );

    watch(
        () => props.search,
        (value) => {
            searchTerm.value = value ?? '';
        },
    );

    const cartOpen = ref(false);
    const cartItems = ref < CartItem[] > ([]);
    const customerEmail = ref < string > ('');
    const groomingInfo = ref({
        owner_name: '',
        owner_phone: '',
        cat_name: '',
        cat_age: '',
        cat_estimated_weight: '',
        notes: '',
    });

    const hasCartItems = computed(() => cartItems.value.length > 0);

    const totalPrice = computed(() => cartItems.value.reduce((total, item) => total + Number(item.price) * Number(item
        .quantity), 0));

    const formattedIdr = (value: number) =>
        Number(value ?? 0).toLocaleString('id-ID', {
            style: 'currency',
            currency: 'IDR',
        });

    const hasGroomingPackage = computed(() => cartItems.value.some((item) => item.type === 'package' && Boolean(item
        .is_grooming)));

    const applyFilter = (filter: string) => {
        selectedFilter.value = filter;
        router.get(
            route('cashier.index'), {
                filter,
                search: searchTerm.value || undefined,
                catalog_page: 1
            }, {
                preserveScroll: true,
                preserveState: true,
                replace: true,
            },
        );
    };

    let searchDebounceTimer: number | null = null;
    watch(
        searchTerm,
        (value) => {
            if (searchDebounceTimer !== null) {
                window.clearTimeout(searchDebounceTimer);
            }

            searchDebounceTimer = window.setTimeout(() => {
                router.get(
                    route('cashier.index'), {
                        filter: selectedFilter.value,
                        search: value || undefined,
                        catalog_page: 1,
                    }, {
                        preserveScroll: true,
                        preserveState: true,
                        replace: true,
                    },
                );
            }, 250);
        }, {
            flush: 'post'
        },
    );

    const addToCart = (item: CatalogItem) => {
        const existing = cartItems.value.find((i) => i.type === item.type && i.id === item.id);
        if (existing) {
            existing.quantity += 1;
            return;
        }

        cartItems.value.push({
            ...item,
            quantity: 1
        });
    };

    const removeFromCart = (item: CartItem) => {
        const index = cartItems.value.findIndex((i) => i.type === item.type && i.id === item.id);
        if (index === -1) return;
        cartItems.value.splice(index, 1);
    };

    const updateQuantity = (item: CartItem, delta: number) => {
        const existing = cartItems.value.find((i) => i.type === item.type && i.id === item.id);
        if (!existing) return;
        existing.quantity += delta;
        if (existing.quantity <= 0) {
            removeFromCart(existing);
        }
    };

    const checkout = () => {
        if (cartItems.value.length === 0) {
            alert('Keranjang masih kosong');
            return;
        }

        const payload = {
            cust_email: customerEmail.value,
            items: cartItems.value.map((item) => ({
                type: item.type,
                id: item.id,
                quantity: item.quantity
            })),
            grooming: hasGroomingPackage.value ? groomingInfo.value : null,
        };

        router.post(route('cashier.checkout'), payload, {
            preserveScroll: true,
            onSuccess: () => {
                cartItems.value = [];
                customerEmail.value = '';
                groomingInfo.value = {
                    owner_name: '',
                    owner_phone: '',
                    cat_name: '',
                    cat_age: '',
                    cat_estimated_weight: '',
                    notes: ''
                };
                cartOpen.value = false;
            },
        });
    };

    const openPendingDetail = (id: number) => {
        router.get(route('cashier.transaction.show', id));
    };

    const markAsSuccess = (id: number) => {
        router.post(route('cashier.markAsSuccess', id), {}, {
            preserveScroll: true
        });
    };

    const markAsFailed = (id: number) => {
        router.post(route('cashier.markAsFailed', id), {}, {
            preserveScroll: true
        });
    };

    const pendingHeaders = [{
            key: 'id',
            label: 'No'
        },
        {
            key: 'unique_id',
            label: 'Kode Unik Transaksi'
        },
        {
            key: 'total_price',
            label: 'Total'
        },
        {
            key: 'actions',
            label: 'Aksi',
            class: 'text-right'
        },
    ];

    const successHeaders = [{
            key: 'id',
            label: 'No'
        },
        {
            key: 'unique_id',
            label: 'Kode Unik Transaksi'
        },
        {
            key: 'total_price',
            label: 'Total'
        },
        {
            key: 'actions',
            label: 'Aksi',
            class: 'text-right'
        },
    ];
</script>

<template>

    <Head title="Kasir" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <Heading variant="small" title="Kasir" description="Tambah transaksi & kelola pembayaran hari ini." />

                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold">Transaksi Pending</h3>
                    </div>
                    <PaginatedTable :headers="pendingHeaders" :paginator="pendingTransactions"
                        empty-text="Tidak ada transaksi pending.">
                        <template #cell-total_price="{ item }">
                            {{ formattedIdr(item . total_price) }}
                        </template>

                        <template #cell-actions="{ item }">
                            <div class="flex justify-end items-center gap-2">
                                <button type="button" @click="openPendingDetail(item.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition"
                                    title="Lihat Detail">
                                    <Eye :size="16" />
                                </button>
                                <button type="button" @click="markAsSuccess(item.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-green-100 text-green-700 hover:bg-green-600 hover:text-white transition"
                                    title="Sukses">
                                    <Check :size="16" />
                                </button>
                                <button type="button" @click="markAsFailed(item.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-red-100 text-red-700 hover:bg-red-600 hover:text-white transition"
                                    title="Failed">
                                    <X :size="16" />
                                </button>
                            </div>
                        </template>
                    </PaginatedTable>
                </div>

                <div class="rounded-2xl border bg-background shadow-sm p-6 space-y-5">
                    <div class="flex flex-col gap-2 ">
                        <h3 class="text-base font-semibold">Produk & Paket</h3>
                        <div class="flex flex-col gap-3">
                            <div class="relative w-full sm:max-w-sm">
                                <span
                                    class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                                    <Search :size="16" />
                                </span>
                                <input v-model="searchTerm" type="text" placeholder="Cari produk atau paket..."
                                    class="flex h-10 w-full rounded-xl border border-input bg-background pl-10 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" @click="applyFilter('all')"
                                    class="rounded-xl px-4 py-2 text-sm border transition"
                                    :class="selectedFilter === 'all' ?
                                        'bg-primary text-primary-foreground border-primary' :
                                        'bg-background border-input hover:bg-muted'">
                                    Semua
                                </button>
                                <button type="button" @click="applyFilter('package')"
                                    class="rounded-xl px-4 py-2 text-sm border transition"
                                    :class="selectedFilter === 'package' ?
                                        'bg-primary text-primary-foreground border-primary' :
                                        'bg-background border-input hover:bg-muted'">
                                    Paket
                                </button>
                                <button v-for="category in categories" :key="category.id" type="button"
                                    @click="applyFilter(`category-${category.id}`)"
                                    class="rounded-xl px-4 py-2 text-sm border transition"
                                    :class="selectedFilter === `category-${category.id}` ?
                                        'bg-primary text-primary-foreground border-primary' :
                                        'bg-background border-input hover:bg-muted'">
                                    {{ category . name }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="item in catalog.data" :key="`${item.type}-${item.id}`"
                            class="rounded-2xl border bg-background shadow-sm overflow-hidden transition hover:shadow-md h-full flex flex-col">
                            <div class="h-36 bg-muted/30 overflow-hidden flex items-center justify-center">
                                <img v-if="item.img_url" :src="item.img_url" :alt="item.name"
                                    class="h-full w-full object-cover" />
                                <div v-else class="text-xs text-muted-foreground">No Image</div>
                            </div>

                            <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="truncate font-medium text-sm">{{ item . name }}</div>
                                        <div v-if="item.type === 'product' && item.sku"
                                            class="truncate text-xs text-muted-foreground">{{ item . sku }}</div>
                                        <div v-if="item.type === 'package' && item.description"
                                            class="truncate text-xs text-muted-foreground">{{ item . description }}
                                        </div>
                                    </div>
                                    <span
                                        class="shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="item.type === 'package' ? 'bg-blue-100 text-blue-700' :
                                            'bg-muted text-foreground'">
                                        {{ item . type === 'package' ? 'Paket' : 'Produk' }}
                                    </span>
                                </div>

                                <div v-if="item.type === 'package' && item.is_grooming" class="text-xs">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700">Grooming</span>
                                </div>

                                <div class="pt-4 flex flex-col gap-2">
                                    <div class="text-sm font-semibold text-primary">
                                        {{ formattedIdr(item . price) }}
                                    </div>

                                    <button type="button" @click="addToCart(item)"
                                        class="w-full inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90 mt-auto">
                                        + Keranjang
                                    </button>
                                </div>

                            </div>
                        </div>

                        <div v-if="!catalog?.data?.length"
                            class="col-span-full py-10 text-center text-sm text-muted-foreground">
                            Tidak ada data.
                        </div>
                    </div>

                    <div v-if="catalog?.links?.length" class="flex flex-wrap items-center justify-end gap-2">
                        <template v-for="(link, idx) in catalog.links" :key="idx">
                            <button v-if="!link.url" type="button" disabled
                                class="rounded-xl border border-input bg-background px-3 py-1.5 text-sm text-muted-foreground opacity-60">
                                <span v-html="link.label" />
                            </button>
                            <Link v-else :href="link.url" preserve-scroll preserve-state
                                class="rounded-xl border px-3 py-1.5 text-sm transition"
                                :class="link.active ? 'bg-primary text-primary-foreground border-primary' :
                                    'border-input bg-background hover:bg-muted'">
                            <span v-html="link.label" />
                            </Link>
                        </template>
                    </div>
                </div>

                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold">Transaksi Success</h3>
                    </div>
                    <PaginatedTable :headers="successHeaders" :paginator="successTransactions"
                        empty-text="Tidak ada transaksi success.">
                        <template #cell-total_price="{ item }">
                            {{ formattedIdr(item . total_price) }}
                        </template>

                        <template #cell-actions="{ item }">
                            <div class="flex justify-end">
                                <Link :href="route('transaction.show', item.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition"
                                    title="Detail">
                                <Eye :size="16" />
                                </Link>
                            </div>
                        </template>
                    </PaginatedTable>
                </div>

                <button v-if="hasCartItems && !cartOpen" type="button" @click="cartOpen = true"
                    class="fixed bottom-6 right-6 inline-flex items-center gap-3 rounded-full bg-primary px-5 py-3 text-sm font-medium text-primary-foreground shadow-lg transition hover:opacity-90">
                    <ShoppingCart :size="18" />
                    <span>{{ cartItems . length }}</span>
                    <span class="opacity-80">•</span>
                    <span>{{ formattedIdr(totalPrice) }}</span>
                </button>

                <div class="fixed inset-0 z-50 pointer-events-none">
                    <Transition enter-active-class="transition-opacity duration-200 ease-out"
                        enter-from-class="opacity-0" enter-to-class="opacity-100"
                        leave-active-class="transition-opacity duration-200 ease-in" leave-from-class="opacity-100"
                        leave-to-class="opacity-0">
                        <div v-if="cartOpen" class="pointer-events-auto absolute inset-0 bg-transparent"
                            @click="cartOpen = false"></div>
                    </Transition>

                    <Transition enter-active-class="transform transition duration-300 ease-out"
                        enter-from-class="translate-x-full" enter-to-class="translate-x-0"
                        leave-active-class="transform transition duration-300 ease-in"
                        leave-from-class="translate-x-0" leave-to-class="translate-x-full">
                        <div v-if="cartOpen"
                            class="pointer-events-auto fixed inset-y-0 right-0 w-full max-w-md bg-background border-l shadow-xl flex flex-col"
                            @click.stop>
                            <div class="h-full flex flex-col">
                                <div class="flex items-center justify-between px-6 py-4">
                                    <div class="font-semibold">Keranjang</div>
                                    <button type="button"
                                        class="rounded-md px-2 py-1 text-muted-foreground hover:bg-muted"
                                        @click="cartOpen = false">
                                        <X :size="18" />
                                    </button>
                                </div>

                                <div class="flex-1 overflow-auto px-6 py-4 space-y-4">
                                    <div v-if="cartItems.length === 0"
                                        class="py-10 text-center text-sm text-muted-foreground">
                                        Keranjang kosong.
                                    </div>

                                    <div v-else class="space-y-3">
                                        <div v-for="item in cartItems" :key="`${item.type}-${item.id}`"
                                            class="flex items-center justify-between gap-3 rounded-xl border p-3">
                                            <div class="min-w-0">
                                                <div class="truncate text-sm font-medium">{{ item . name }}</div>
                                                <div class="text-xs text-muted-foreground">
                                                    {{ item . type === 'product' ? 'Produk' : 'Paket' }}</div>
                                                <div class="text-xs font-semibold text-primary">
                                                    {{ formattedIdr(item . price) }}</div>
                                            </div>

                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <button type="button" @click="updateQuantity(item, -1)"
                                                    class="flex h-7 w-7 items-center justify-center rounded-md bg-muted text-sm font-bold hover:bg-muted/80 transition">−</button>
                                                <span class="w-7 text-center text-sm">{{ item . quantity }}</span>
                                                <button type="button" @click="updateQuantity(item, 1)"
                                                    class="flex h-7 w-7 items-center justify-center rounded-md bg-muted text-sm font-bold hover:bg-muted/80 transition">+</button>
                                                <button type="button" @click="removeFromCart(item)"
                                                    class="flex h-7 w-7 items-center justify-center rounded-md bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition text-sm font-bold">×</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between border-t pt-4 font-semibold">
                                        <span class="text-sm">Total</span>
                                        <span class="text-sm">{{ formattedIdr(totalPrice) }}</span>
                                    </div>

                                    <div>
                                        <label for="customer_email" class="mb-1 block text-sm font-medium">Email
                                            Customer</label>
                                        <input id="customer_email" v-model="customerEmail" type="email"
                                            placeholder="contoh@email.com"
                                            class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2" />
                                    </div>

                                    <div v-if="hasGroomingPackage"
                                        class="rounded-xl border bg-muted/30 p-4 space-y-3">
                                        <div class="text-sm font-semibold">Informasi Grooming</div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium">Nama Pemilik</label>
                                            <input v-model="groomingInfo.owner_name" type="text"
                                                placeholder="Nama pemilik"
                                                class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium">No. Telp Pemilik</label>
                                            <input v-model="groomingInfo.owner_phone" type="text"
                                                placeholder="08xx"
                                                class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium">Nama Kucing</label>
                                            <input v-model="groomingInfo.cat_name" type="text"
                                                placeholder="Nama kucing"
                                                class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium">Usia Kucing</label>
                                            <input v-model="groomingInfo.cat_age" type="number"
                                                placeholder="Usia (tahun)"
                                                class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium">Berat Perkiraan (kg)</label>
                                            <input v-model="groomingInfo.cat_estimated_weight" type="number"
                                                step="0.01" placeholder="0.00"
                                                class="flex h-9 w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium">Catatan</label>
                                            <textarea v-model="groomingInfo.notes" placeholder="Catatan tambahan" rows="2"
                                                class="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t px-6 py-4">
                                    <button type="button" @click="checkout"
                                        class="w-full inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90">
                                        Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
