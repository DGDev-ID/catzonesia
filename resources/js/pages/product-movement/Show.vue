<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { formatCurrency } from '@/lib/utils';

const props = defineProps<{
    product: any;
    movements: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Product Movement', href: '/product-movement' },
    { title: props.product.name, href: `/product-movement/${props.product.id}` },
];

const movementType = ref<'inbound' | 'outbound'>('inbound');
const quantity = ref<number>(0);
const unitId = ref<number | null>(props.product?.base_unit_id ?? null);
const price = ref<number | null>(null);
const note = ref('');

const headers = [
    { key: 'id', label: 'No' },
    { key: 'type', label: 'Tipe' },
    { key: 'opening_stock', label: 'Stok Awal', suffix: props.product?.base_unit?.name ?? '' },
    { key: 'quantity', label: 'Jumlah' },
    { key: 'unit.name', label: 'Satuan' },
    { key: 'closing_stock', label: 'Stok Akhir', suffix: props.product?.base_unit?.name ?? '' },
    { key: 'price', label: 'Harga', formatter: formatCurrency },
    { key: 'created_at', label: 'Tanggal' },
    { key: 'note', label: 'Catatan' },
];

const availableUnits = computed(() => {
    const units: any[] = [];

    if (props.product?.base_unit) {
        units.push(props.product.base_unit);
    }

    const converters = props.product?.product_unit_converters ?? [];
    for (const conv of converters) {
        if (conv?.unit_from && !units.some((u) => u.id === conv.unit_from.id)) {
            units.push(conv.unit_from);
        }
        if (conv?.unit_to && !units.some((u) => u.id === conv.unit_to.id)) {
            units.push(conv.unit_to);
        }
    }

    return units;
});

const adjustStock = () => {
    if (!movementType.value || quantity.value <= 0 || !unitId.value) {
        alert('Masukkan jumlah dan pilih unit');
        return;
    }

    if (movementType.value === 'inbound' && (price.value === null || Number.isNaN(price.value))) {
        alert('Masukkan harga untuk inbound');
        return;
    }

    router.post(
        route('product-movement.adjustment', props.product.id),
        {
            type: movementType.value,
            quantity: quantity.value,
            unit_id: unitId.value,
            note: note.value,
            price: movementType.value === 'inbound' ? price.value : null,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        },
    );
};
</script>

<template>
    <Head title="Product Movement" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" :title="props.product.name" description="Pergerakan stok & penyesuaian." />
                    <Link href="/product-movement" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <div class="rounded-2xl border bg-background shadow-sm p-8">
                    <h3 class="mb-6 text-base font-semibold">Data Produk</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-if="props.product.img_url" class="md:col-span-1">
                            <div class="overflow-hidden rounded-xl border bg-muted/30">
                                <img :src="props.product.img_url" alt="Gambar Produk" class="h-48 w-full object-contain bg-background" />
                            </div>
                        </div>

                        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-muted-foreground">Nama</div>
                                <div class="font-medium">{{ props.product.name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">SKU</div>
                                <div class="font-medium">{{ props.product.sku }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">Stok</div>
                                <div class="font-medium">{{ props.product.stock }} {{ props.product.base_unit?.name ?? '' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">Stok Alert</div>
                                <div class="font-medium">{{ props.product.stock_alert }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Adjust Stock Card -->
                <div class="rounded-2xl border bg-background shadow-sm p-8">
                    <h3 class="mb-6 text-base font-semibold">Penyesuaian Stok</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="movement_type" class="mb-1 block text-sm font-medium">Tipe</label>
                            <select
                                id="movement_type"
                                v-model="movementType"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option value="inbound">Inbound</option>
                                <option value="outbound">Outbound</option>
                            </select>
                        </div>
                        <div>
                            <label for="quantity" class="mb-1 block text-sm font-medium">Jumlah</label>
                            <input
                                id="quantity"
                                v-model="quantity"
                                type="number"
                                min="1"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>
                        <div>
                            <label for="unit_id" class="mb-1 block text-sm font-medium">Unit</label>
                            <select
                                id="unit_id"
                                v-model="unitId"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                                <option :value="null" disabled>Pilih Unit</option>
                                <option v-for="unit in availableUnits" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                            </select>
                        </div>
                        <div v-if="movementType === 'inbound'">
                            <label for="price" class="mb-1 block text-sm font-medium">Harga</label>
                            <input
                                id="price"
                                v-model="price"
                                type="number"
                                min="0"
                                step="0.01"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>
                        <div>
                            <label for="note" class="mb-1 block text-sm font-medium">Catatan</label>
                            <input
                                id="note"
                                v-model="note"
                                type="text"
                                placeholder="Opsional"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>
                    </div>
                    <div class="mt-4">
                        <button
                            type="button"
                            @click="adjustStock"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                        >
                            Sesuaikan Stok
                        </button>
                    </div>
                </div>

                <!-- Movement Table -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b">
                        <h3 class="text-base font-semibold">Mutasi Stok</h3>
                    </div>
                    <PaginatedTable :headers="headers" :paginator="movements">
                        <template #cell-type="{ item }">
                            <span
                                :class="item.type === 'inbound' ? 'bg-green-100 text-green-700' : item.type === 'outbound' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize"
                            >
                                {{ item.type }}
                            </span>
                        </template>
                    </PaginatedTable>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
