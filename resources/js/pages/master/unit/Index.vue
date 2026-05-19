<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';

defineProps<{
    units: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Unit', href: '/master/unit' },
];

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Nama Unit' },
    { key: 'actions', label: 'Aksi' },
];

const deleteUnit = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus unit ini?')) {
        router.delete(`/api/master/unit/${id}`);
    }
};
</script>

<template>
    <Head title="Unit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <Heading variant="small" title="Master Unit" description="Kelola daftar unit pengukuran produk." />
                    <Link href="/master/unit/create"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90">
                        Tambah Unit
                    </Link>
                </div>

                <!-- Table Card -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <PaginatedTable :headers="headers" :paginator="units">
                        <template #cell-actions="{ item }">
                            <div class="flex justify-start items-center gap-3">
                                <Link :href="route('master.unit.edit', item.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-yellow-100 text-yellow-600 hover:bg-yellow-500 hover:text-white transition"
                                    title="Edit Unit">
                                    <Pencil :size="16" />
                                </Link>
                                <button @click="deleteUnit(item.id)" type="button"
                                    class="cursor-pointer inline-flex items-center justify-center w-8 h-8 rounded-md bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition"
                                    title="Hapus Unit">
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
