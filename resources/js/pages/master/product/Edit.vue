<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps<{
    product: any;
    categories: any;
    units: any;
}>();

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

    <AppLayout>
        <div class="container mx-auto py-8">
            <div class="mb-8 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Edit Product</h1>
                <button
                    type="button"
                    @click="router.visit('/products')"
                    class="rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                >
                    Back
                </button>
            </div>

            <div class="rounded-md bg-white p-6 shadow-md">
                <form @submit.prevent="handleSubmit">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label for="sku" class="mb-1 block text-sm font-medium text-gray-700">SKU</label>
                            <input
                                id="sku"
                                v-model="form.sku"
                                type="text"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label for="price" class="mb-1 block text-sm font-medium text-gray-700">Price</label>
                            <input
                                id="price"
                                v-model="form.price"
                                type="number"
                                step="0.01"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label for="stock" class="mb-1 block text-sm font-medium text-gray-700">Stock</label>
                            <input
                                id="stock"
                                v-model="form.stock"
                                type="number"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label for="base_unit_id" class="mb-1 block text-sm font-medium text-gray-700">Base Unit</label>
                            <select
                                id="base_unit_id"
                                v-model="form.base_unit_id"
                                required
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Select unit</option>
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label for="is_display" class="mb-1 block text-sm font-medium text-gray-700">Display</label>
                            <input id="is_display" v-model="form.is_display" type="checkbox" class="mt-1" />
                        </div>

                        <div class="col-span-2">
                            <label for="description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            ></textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Categories</label>
                            <div class="grid grid-cols-4 gap-2">
                                <label v-for="category in categories" :key="category.id" class="flex items-center">
                                    <input type="checkbox" v-model="form.categories" :value="category.id" class="mr-2" />
                                    {{ category.name }}
                                </label>
                            </div>
                        </div>

                        <div class="col-span-2">
                            <div class="mb-4 flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">Unit Converters</label>
                                <button
                                    type="button"
                                    @click="addUnitConverter"
                                    class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    Add Converter
                                </button>
                            </div>

                            <div v-if="form.unitConverters.length === 0" class="py-4 text-center text-gray-500">No unit converters added yet.</div>

                            <div v-for="(converter, index) in form.unitConverters" :key="index" class="mb-4 flex gap-4">
                                <select
                                    v-model="converter.unit_id"
                                    required
                                    class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Select unit</option>
                                    <option
                                        v-for="unitOpt in availableUnits"
                                        :key="unitOpt.id"
                                        :value="unitOpt.id"
                                    >
                                        {{ unitOpt.name }}
                                    </option>
                                </select>

                                <input
                                v-model.number="converter.multiplier"
                                type="number"
                                step="0.0001"
                                required
                                placeholder="Multiplier"
                                class="flex-1 rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />

                                <button
                                    type="button"
                                    @click="removeUnitConverter(index)"
                                    class="rounded-md bg-red-600 px-4 py-2 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                                >
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <button
                            type="button"
                            @click="router.visit('/products')"
                            class="rounded-md bg-gray-600 px-4 py-2 text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>