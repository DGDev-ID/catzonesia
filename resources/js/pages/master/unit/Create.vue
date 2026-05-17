<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Master',
        href: '/master',
    },
    {
        title: 'Unit',
        href: '/master/unit',
    },
    {
        title: 'Create',
        href: '/master/unit/create',
    },
];

const name = ref('');

const handleSubmit = () => {
    if (!name.value.trim()) {
        alert('Please enter a unit name');
        return;
    }
    router.post('/api/master/unit', { name: name.value.trim() });
};
</script>

<template>
    <Head title="Create Unit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Create Unit</h1>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-md">
                <div class="mb-4">
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                    <input
                        id="name"
                        v-model="name"
                        type="text"
                        class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter unit name"
                    />
                </div>

                <div class="flex justify-end gap-2">
                    <button @click="router.visit('/master/unit')" class="rounded-md border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="handleSubmit" class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">Create</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
