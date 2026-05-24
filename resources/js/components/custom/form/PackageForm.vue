<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface PackageProduct {
    product_id: number;
    quantity: number;
    unit_id: number;
}

interface PackageFormData {
    name: string;
    price: number;
    is_grooming: boolean;
    description: string;
    products: PackageProduct[];
}

const props = withDefaults(defineProps<{
    initialData?: Partial<PackageFormData>;
    submitLabel?: string;
    backUrl?: string;
    products?: any[];
    units?: any[];
}>(), {
    submitLabel: 'Simpan',
    backUrl: '/master/package',
    products: () => [],
    units: () => [],
});

const emit = defineEmits<{
    submit: [data: PackageFormData];
}>();

const form = ref<PackageFormData>({
    name: props.initialData?.name ?? '',
    price: props.initialData?.price ?? 0,
    is_grooming: props.initialData?.is_grooming ?? false,
    description: props.initialData?.description ?? '',
    products: props.initialData?.products ?? [],
});

const selectedProductId = ref<number | null>(null);
const productQuantity = ref(1);
const productUnitId = ref<number | null>(null);

// Computed property to get available units for selected product
const availableUnits = computed(() => {
    if (!selectedProductId.value) {
        return [];
    }

    const selectedProduct = props.products.find(
        p => p.id === selectedProductId.value
    );

    if (!selectedProduct) {
        return [];
    }

    const units: any[] = [];

    // base_unit
    if (selectedProduct.base_unit) {
        units.push(selectedProduct.base_unit);
    }

    // product_unit_converters
    if (selectedProduct.product_unit_converters) {
        selectedProduct.product_unit_converters.forEach((converter: any) => {

            // unit_from
            if (
                converter.unit_from &&
                !units.find(u => u.id === converter.unit_from.id)
            ) {
                units.push(converter.unit_from);
            }

            // unit_to
            if (
                converter.unit_to &&
                !units.find(u => u.id === converter.unit_to.id)
            ) {
                units.push(converter.unit_to);
            }
        });
    }

    return units;
});

const handleProductChange = (productId: number | null) => {
    selectedProductId.value = productId;
    productQuantity.value = 1;
    productUnitId.value = null;

    if (productId) {
        const selectedProduct = props.products.find(
            p => p.id === productId
        );

        if (selectedProduct) {

            // Set default unit ke base unit
            if (selectedProduct.base_unit) {
                productUnitId.value = selectedProduct.base_unit.id;

            } else if (
                selectedProduct.product_unit_converters &&
                selectedProduct.product_unit_converters.length > 0
            ) {

                // fallback ke converter pertama
                if (selectedProduct.product_unit_converters[0].unit_from) {
                    productUnitId.value =
                        selectedProduct.product_unit_converters[0]
                            .unit_from.id;

                } else if (
                    selectedProduct.product_unit_converters[0].unit_to
                ) {
                    productUnitId.value =
                        selectedProduct.product_unit_converters[0]
                            .unit_to.id;
                }
            }
        }
    }
};

const handleAddProduct = () => {
    if (!selectedProductId.value || !productQuantity.value || !productUnitId.value) {
        alert('Pilih produk, masukkan jumlah, dan pilih unit');
        return;
    }

    // Check if product already exists
    const existingProduct = form.value.products.find(p => p.product_id === selectedProductId.value);
    if (existingProduct) {
        alert('Produk sudah ditambahkan ke paket');
        return;
    }

    form.value.products.push({
        product_id: selectedProductId.value,
        quantity: productQuantity.value,
        unit_id: productUnitId.value,
    });

    // Reset form
    selectedProductId.value = null;
    productQuantity.value = 1;
    productUnitId.value = null;
};

const handleRemoveProduct = (index: number) => {
    form.value.products.splice(index, 1);
};

const handleSubmit = () => {
    if (form.value.products.length === 0 && !form.value.is_grooming) {
        alert('Paket tidak grooming harus memiliki setidaknya satu produk');
        return;
    }

    emit('submit', { ...form.value });
};
</script>

<template>
    <div class="rounded-2xl border bg-background shadow-sm p-8">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label for="pkg-name" class="mb-2 block text-sm font-medium">Nama Paket</label>
                <input
                    id="pkg-name"
                    v-model="form.name"
                    type="text"
                    placeholder="Masukkan nama paket"
                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                />
            </div>

            <div>
                <label for="pkg-price" class="mb-2 block text-sm font-medium">Harga</label>
                <input
                    id="pkg-price"
                    v-model="form.price"
                    type="number"
                    placeholder="Masukkan harga"
                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                />
            </div>

            <div>
                <label for="pkg-desc" class="mb-2 block text-sm font-medium">Deskripsi</label>
                <textarea
                    id="pkg-desc"
                    v-model="form.description"
                    rows="3"
                    placeholder="Masukkan deskripsi paket"
                    class="flex w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                ></textarea>
            </div>

            <div class="flex items-center gap-3">
                <input
                    id="pkg-grooming"
                    v-model="form.is_grooming"
                    type="checkbox"
                    class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                />
                <label for="pkg-grooming" class="text-sm font-medium">Paket Grooming</label>
            </div>

            <!-- Product Selection -->
            <div v-if="!form.is_grooming" class="border-t pt-6">
                <h3 class="mb-4 text-base font-semibold">Produk dalam Paket</h3>

                <!-- Add Product Form -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label for="product-select" class="mb-2 block text-sm font-medium">Pilih Produk</label>
                        <select
                            id="product-select"
                            v-model="selectedProductId"
                            @change="handleProductChange(selectedProductId)"
                            class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        >
                            <option :value="null" disabled>-- Pilih Produk --</option>
                            <option v-for="product in props.products" :key="product.id" :value="product.id">
                                {{ product.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="product-quantity" class="mb-2 block text-sm font-medium">Jumlah</label>
                        <input
                            id="product-quantity"
                            v-model="productQuantity"
                            type="number"
                            min="1"
                            placeholder="Jumlah"
                            class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        />
                    </div>

                    <div>
                        <label for="product-unit" class="mb-2 block text-sm font-medium">Satuan</label>
                        <select
                            id="product-unit"
                            v-model="productUnitId"
                            class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            :disabled="!selectedProductId"
                        >
                            <option :value="null" disabled>-- Pilih Satuan --</option>
                            <option v-for="unit in availableUnits" :key="unit.id" :value="unit.id">
                                {{ unit.name }}
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end justify-end">
                        <button
                            type="button"
                            @click="handleAddProduct"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                            :disabled="!selectedProductId || !productQuantity || !productUnitId"
                        >
                            Tambah Produk
                        </button>
                    </div>
                </div>

                <!-- Added Products List -->
                <div v-if="form.products.length > 0" class="space-y-3">
                    <div
                        v-for="(product, index) in form.products"
                        :key="index"
                        class="flex items-center justify-between p-4 rounded-xl bg-muted/30"
                    >
                        <div>
                            <div class="font-medium">
                                {{ products.find(p => p.id === product.product_id)?.name }}
                            </div>
                            <div class="text-sm text-muted-foreground">
                                Jumlah: {{ product.quantity }} {{ units.find(u => u.id === product.unit_id)?.name }}
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="handleRemoveProduct(index)"
                            class="inline-flex items-center justify-center rounded-xl bg-red-100 px-3 py-1 text-sm font-medium text-red-600 transition hover:bg-red-600 hover:text-white"
                        >
                            Hapus
                        </button>
                    </div>
                </div>

                <div v-if="form.products.length === 0" class="text-sm text-muted-foreground text-center py-4">
                    Belum ada produk ditambahkan ke paket
                </div>
            </div>
        </div>

        <div class="mt-8 flex gap-3 justify-end">
            <button
                type="button"
                @click="handleSubmit"
                class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90 w-full"
            >
                {{ submitLabel }}
            </button>
        </div>
    </div>
</template>
