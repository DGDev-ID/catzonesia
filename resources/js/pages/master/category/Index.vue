<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';

defineProps<{
    categories: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Category', href: '/master/category' },
];

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Nama Kategori' },
    { key: 'actions', label: 'Aksi' },
];

const deleteCategory = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
        router.delete(route('master.category.destroy', id));
    }
};
</script>

<template>
    <Head title="Category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <Heading variant="small" title="Master Category" description="Kelola daftar kategori produk." />
                    <Link
                        :href="route('master.category.create')"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                    >
                        Tambah Category
                    </Link>
                </div>

                <!-- Table Card -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <PaginatedTable :headers="headers" :paginator="categories">
                        <template #cell-actions="{ item }">
                            <div class="flex justify-start items-center gap-3">
                                <Link
                                    :href="route('master.category.edit', item.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-yellow-100 text-yellow-600 hover:bg-yellow-500 hover:text-white transition"
                                    title="Edit Category"
                                >
                                    <Pencil :size="16" />
                                </Link>
                                <button
                                    @click="deleteCategory(item.id)"
                                    type="button"
                                    class="cursor-pointer inline-flex items-center justify-center w-8 h-8 rounded-md bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition"
                                    title="Hapus Category"
                                >
                                    <Trash2 :size="16" />
                                </button>
                            </div>
                        </template>
                    </PaginatedTable>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
