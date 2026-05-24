<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface UnitConverter {
    unit_from_id: string | number;
    unit_to_id: string | number;
    multiplier: string | number;
}

interface ProductFormData {
    name: string;
    sku: string;
    price: string | number;
    stock: string | number;
    base_unit_id: string | number;
    description: string;
    is_display: boolean;
    categories: number[];
    unitConverters: UnitConverter[];
}

const props = withDefaults(defineProps<{
    categories: any[];
    units: any[];
    initialData?: Partial<ProductFormData>;
    submitLabel?: string;
    backUrl?: string;
}>(), {
    submitLabel: 'Simpan',
    backUrl: '/master/product',
});

const emit = defineEmits<{
    submit: [data: ProductFormData];
}>();

const form = ref<ProductFormData>({
    name: props.initialData?.name ?? '',
    sku: props.initialData?.sku ?? '',
    price: props.initialData?.price ?? '',
    stock: props.initialData?.stock ?? '',
    base_unit_id: props.initialData?.base_unit_id ?? '',
    description: props.initialData?.description ?? '',
    is_display: props.initialData?.is_display ?? true,
    categories: props.initialData?.categories ?? [],
    unitConverters: props.initialData?.unitConverters ?? [],
});

const addUnitConverter = () => {
    form.value.unitConverters.push({ unit_from_id: '', unit_to_id: '', multiplier: '' });
};

const removeUnitConverter = (index: number) => {
    form.value.unitConverters.splice(index, 1);
};

const handleSubmit = () => {
    emit('submit', { ...form.value });
};
</script>

<template>
    <div class="rounded-2xl border bg-background shadow-sm p-8">
        <form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="prod-name" class="mb-2 block text-sm font-medium">Nama Produk</label>
                    <input
                        id="prod-name"
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="Masukkan nama produk"
                        class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    />
                </div>

                <div>
                    <label for="prod-sku" class="mb-2 block text-sm font-medium">SKU</label>
                    <input
                        id="prod-sku"
                        v-model="form.sku"
                        type="text"
                        required
                        placeholder="Masukkan SKU"
                        class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    />
                </div>

                <div>
                    <label for="prod-price" class="mb-2 block text-sm font-medium">Harga</label>
                    <input
                        id="prod-price"
                        v-model="form.price"
                        type="number"
                        step="0.01"
                        required
                        placeholder="Masukkan harga"
                        class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    />
                </div>

                <div>
                    <label for="prod-stock" class="mb-2 block text-sm font-medium">Stok</label>
                    <input
                        id="prod-stock"
                        v-model="form.stock"
                        type="number"
                        required
                        placeholder="Masukkan stok awal"
                        class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    />
                </div>

                <div>
                    <label for="prod-base-unit" class="mb-2 block text-sm font-medium">Unit Dasar</label>
                    <select
                        id="prod-base-unit"
                        v-model="form.base_unit_id"
                        required
                        class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                    >
                        <option value="" disabled selected>Pilih unit</option>
                        <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-6">
                    <input id="prod-is-display" v-model="form.is_display" type="checkbox" class="h-4 w-4 rounded border-input text-primary focus:ring-ring" />
                    <label for="prod-is-display" class="text-sm font-medium">Tampilkan di kasir</label>
                </div>

                <div class="col-span-2">
                    <label for="prod-desc" class="mb-2 block text-sm font-medium">Deskripsi</label>
                    <textarea
                        id="prod-desc"
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
                            v-model="converter.unit_from_id"
                            required
                            class="flex h-10 flex-1 rounded-xl border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="" disabled selected>-- Unit From --</option>
                            <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                {{ unit.name }}
                            </option>
                        </select>

                        <select
                            v-model="converter.unit_to_id"
                            required
                            class="flex h-10 flex-1 rounded-xl border border-input bg-background px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="" disabled selected>-- Unit To --</option>
                            <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                {{ unit.name }}
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
                    class="w-full inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                >
                    {{ submitLabel }}
                </button>
            </div>
        </form>
    </div>
</template>
