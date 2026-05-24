<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    packages: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Package', href: '/master/package' },
];

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Nama Paket' },
    { key: 'price', label: 'Harga' },
    { key: 'is_grooming', label: 'Grooming' },
    { key: 'description', label: 'Deskripsi' },
    { key: 'actions', label: 'Aksi' },
];

const deletePackage = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus paket ini?')) {
        router.delete(route('master.package.destroy', id));
    }
};
</script>

<template>
    <Head title="Package" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <!-- Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <Heading variant="small" title="Master Package" description="Kelola daftar paket layanan." />
                    <Link
                        :href="route('master.package.create')"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                    >
                        Tambah Package
                    </Link>
                </div>

                <!-- Table Card -->
                <div class="rounded-2xl border bg-background shadow-sm overflow-hidden">
                    <PaginatedTable :headers="headers" :paginator="packages">
                        <template #cell-is_grooming="{ item }">
                            <span
                                :class="item.is_grooming ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                            >
                                {{ item.is_grooming ? 'Ya' : 'Tidak' }}
                            </span>
                        </template>
                        <template #cell-actions="{ item }">
                            <div class="flex justify-end items-center gap-3">
                                <Link
                                    :href="route('master.package.edit', item.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-yellow-100 text-yellow-600 hover:bg-yellow-500 hover:text-white transition"
                                    title="Edit Package"
                                >
                                    <Pencil :size="16" />
                                </Link>
                                <button
                                    type="button"
                                    @click="deletePackage(item.id)"
                                    class="cursor-pointer inline-flex items-center justify-center w-8 h-8 rounded-md bg-red-100 text-red-600 hover:bg-red-600 hover:text-white transition"
                                    title="Hapus Package"
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
