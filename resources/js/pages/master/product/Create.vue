<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps<{
    categories: any;
    units: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Product', href: '/products' },
    { title: 'Create', href: '/products/create' },
];

const availableUnits = computed(() => {
    return props.units.filter((unit: any) => unit.id !== parseInt(form.value.base_unit_id));
});

const form = ref({
    name: '',
    sku: '',
    price: '',
    stock: '',
    base_unit_id: '',
    description: '',
    is_display: true,
    categories: [] as number[],
    unitConverters: [] as { unit_id: string; multiplier: string }[],
});

const handleSubmit = () => {
    const payload = {
        ...form.value,
        price: parseFloat(form.value.price),
        stock: parseInt(form.value.stock),
        base_unit_id: parseInt(form.value.base_unit_id),
        unitConverters: form.value.unitConverters.map((converter) => ({
            unit_id: parseInt(converter.unit_id),
            multiplier: parseFloat(converter.multiplier),
        })),
    };

    router.post('/api/products', payload);
};

const addUnitConverter = () => {
    form.value.unitConverters.push({ unit_id: '', multiplier: '' });
};

const removeUnitConverter = (index: number) => {
    form.value.unitConverters.splice(index, 1);
};
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Tambah Product" description="Tambah produk baru ke dalam sistem." />
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
                                    placeholder="Masukkan nama produk"
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
                                    placeholder="Masukkan SKU"
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
                                    placeholder="Masukkan harga"
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
                                    placeholder="Masukkan stok awal"
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
                                    placeholder="Masukkan deskripsi produk"
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
                                        v-model="converter.multiplier"
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
                                Simpan Product
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