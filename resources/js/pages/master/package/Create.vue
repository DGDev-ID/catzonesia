<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const name = ref('');
const price = ref(0);
const isGrooming = ref(false);
const description = ref('');

const submit = () => {
    router.post(
        '/api/master/package',
        {
            name: name.value,
            price: price.value,
            is_grooming: isGrooming.value,
            description: description.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.get('/master/package');
            },
        },
    );
};
</script>

<template>
    <Head title="Create Package" />

    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="mb-8 text-2xl font-bold">Create Package</h1>

            <div class="max-w-md">
                <div class="mb-4">
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Name</label>
                    <input
                        id="name"
                        v-model="name"
                        type="text"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div class="mb-4">
                    <label for="price" class="mb-2 block text-sm font-medium text-gray-700">Price</label>
                    <input
                        id="price"
                        v-model="price"
                        type="number"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div class="mb-4">
                    <label for="is_grooming" class="mb-2 block text-sm font-medium text-gray-700">Is Grooming</label>
                    <input id="is_grooming" v-model="isGrooming" type="checkbox" class="h-4 w-4 rounded text-blue-600 focus:ring-blue-500" />
                </div>

                <div class="mb-4">
                    <label for="description" class="mb-2 block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                        id="description"
                        v-model="description"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                </div>

                <div class="flex gap-4">
                    <button
                        type="button"
                        @click="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Create Package
                    </button>
                    <button
                        type="button"
                        @click="router.get('/master/package')"
                        class="rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
