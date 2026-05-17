<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps<{
    product: any;
    categories: any;
    units: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Product', href: '/products' },
    { title: 'Edit', href: `/products/edit/${props.product.id}` },
];

const form = ref({
    name: props.product.name,
    sku: props.product.sku,
    price: props.product.price,
    stock: props.product.stock,
    base_unit_id: props.product.base_unit_id,
    description: props.product.description,
    is_display: props.product.is_display,
    categories: props.product.categories.map((cat: any) => cat.id),
    unitConverters: props.product.product_unit_converters.map((conv: any) => ({
        unit_id: conv.unit_id,
        multiplier: conv.multiplier,
    })),
});

const availableUnits = computed(() => {
    return props.units.filter((unit: any) => unit.id !== form.value.base_unit_id);
});

const handleSubmit = () => {
    const payload = {
        ...form.value,
        unitConverters: form.value.unitConverters.map((converter: any) => ({
            unit_id: Number(converter.unit_id),
            multiplier: Number(converter.multiplier),
        })),
    };

    router.put(`/api/products/${props.product.id}`, payload);
};

const addUnitConverter = () => {
    form.value.unitConverters.push({ unit_id: null, multiplier: null });
};

const removeUnitConverter = (index: number) => {
    form.value.unitConverters.splice(index, 1);
};
</script>

<template>
    <Head title="Edit Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Edit Product" description="Perbarui data produk yang sudah ada." />
                    <Link href="/products" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <div class="rounded-2xl border bg-background shadow-sm p-8">
                    <form @submit.prevent="handleSubmit">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="mb-2 block text-sm font-medium">Nama Produk</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                />
                            </div>

                            <div>
                                <label for="sku" class="mb-2 block text-sm font-medium">SKU</label>
                                <input
                                    id="sku"
                                    v-model="form.sku"
                                    type="text"
                                    required
                                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                />
                            </div>

                            <div>
                                <label for="price" class="mb-2 block text-sm font-medium">Harga</label>
                                <input
                                    id="price"
                                    v-model="form.price"
                                    type="number"
                                    step="0.01"
                                    required
                                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                />
                            </div>

                            <div>
                                <label for="stock" class="mb-2 block text-sm font-medium">Stok</label>
                                <input
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    required
                                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                />
                            </div>

                            <div>
                                <label for="base_unit_id" class="mb-2 block text-sm font-medium">Unit Dasar</label>
                                <select
                                    id="base_unit_id"
                                    v-model="form.base_unit_id"
                                    required
                                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                >
                                    <option value="">Pilih unit</option>
                                    <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-3 pt-6">
                                <input id="is_display" v-model="form.is_display" type="checkbox" class="h-4 w-4 rounded border-input text-primary focus:ring-ring" />
                                <label for="is_display" class="text-sm font-medium">Tampilkan di kasir</label>
                            </div>

                            <div class="col-span-2">
                                <label for="description" class="mb-2 block text-sm font-medium">Deskripsi</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="flex w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                ></textarea>
                            </div>

                            <div class="col-span-2">
                                <label class="mb-3 block text-sm font-medium">Kategori</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <label v-for="category in categories" :key="category.id" class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" v-model="form.categories" :value="category.id" class="h-4 w-4 rounded border-input text-primary focus:ring-ring" />
                                        {{ category.name }}
                                    </label>
                                </div>
                            </div>

                            <div class="col-span-2">
                                <div class="mb-4 flex items-center justify-between">
                                    <label class="block text-sm font-medium">Unit Converter</label>
                                    <button
                                        type="button"
                                        @click="addUnitConverter"
                                        class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                                    >
                                        + Tambah Converter
                                    </button>
                                </div>

                                <div v-if="form.unitConverters.length === 0" class="py-4 text-center text-sm text-muted-foreground rounded-xl border border-dashed">
                                    Belum ada unit converter.
                                </div>

                                <div v-for="(converter, index) in form.unitConverters" :key="index" class="mb-3 flex gap-3">
                                    <select
                                        v-model="converter.unit_id"
                                        required
                                        class="flex h-10 flex-1 rounded-xl border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    >
                                        <option value="">Pilih unit</option>
                                        <option v-for="unitOpt in availableUnits" :key="unitOpt.id" :value="unitOpt.id">
                                            {{ unitOpt.name }}
                                        </option>
                                    </select>

                                    <input
                                        v-model.number="converter.multiplier"
                                        type="number"
                                        step="0.0001"
                                        required
                                        placeholder="Multiplier"
                                        class="flex h-10 flex-1 rounded-xl border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    />

                                    <button
                                        type="button"
                                        @click="removeUnitConverter(index)"
                                        class="inline-flex items-center justify-center rounded-xl bg-red-100 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-600 hover:text-white"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                            >
                                Update Product
                            </button>
                            <Link
                                href="/products"
                                class="inline-flex items-center justify-center rounded-xl border border-input bg-background px-5 py-2.5 text-sm font-medium text-foreground shadow-sm transition hover:bg-muted"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>