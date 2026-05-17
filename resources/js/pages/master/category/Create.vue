<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    errors?: Record<string, string[]>;
}>();

const name = ref('');

const submit = () => {
    router.post(
        '/api/master/category',
        {
            name: name.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.get('/master/category');
            },
        },
    );
};
</script>

<template>
    <Head title="Create Category" />

    <AppLayout>
        <div class="container mx-auto py-8">
            <h1 class="mb-8 text-2xl font-bold">Create Category</h1>

            <div class="max-w-md">
                <div class="mb-4">
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Name</label>
                    <input
                        id="name"
                        v-model="name"
                        type="text"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <div v-if="errors?.name" class="mt-1 text-sm text-red-600">
                        {{ errors.name[0] }}
                    </div>
                </div>

                <div class="flex gap-4">
                    <button
                        type="button"
                        @click="submit"
                        class="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Create Category
                    </button>
                    <button
                        type="button"
                        @click="router.get('/master/category')"
                        class="rounded-md bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
