<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    errors?: Record<string, string[]>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Category', href: '/master/category' },
    { title: 'Create', href: '/master/category/create' },
];

const name = ref('');

const submit = () => {
    router.post(
        '/api/master/category',
        { name: name.value },
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

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Tambah Category" description="Tambah kategori produk baru." />
                    <Link href="/master/category" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <div class="rounded-2xl border bg-background shadow-sm p-8 max-w-md">
                    <div class="mb-6">
                        <label for="name" class="mb-2 block text-sm font-medium">Nama Kategori</label>
                        <input
                            id="name"
                            v-model="name"
                            type="text"
                            placeholder="Masukkan nama kategori"
                            class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        />
                        <div v-if="errors?.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                        >
                            Simpan Category
                        </button>
                        <Link
                            href="/master/category"
                            class="inline-flex items-center justify-center rounded-xl border border-input bg-background px-5 py-2.5 text-sm font-medium text-foreground shadow-sm transition hover:bg-muted"
                        >
                            Batal
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
