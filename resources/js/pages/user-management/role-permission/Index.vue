<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';

defineProps<{
    roles: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/user-management/user' },
    { title: 'Role & Permission', href: '/user-management/role' },
];

const headers = [
    { key: 'id', label: 'No' },
    { key: 'name', label: 'Nama Role' },
    { key: 'permissions_count', label: 'Jumlah Permission' },
    { key: 'actions', label: 'Aksi' },
];

const deleteRole = (id: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus role ini?')) {
        router.delete(`/user-management/role/${id}`);
    }
};
</script>

<template>
    <Head title="Role & Permission" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">

                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <Heading variant="small" title="Role & Permission" description="Kelola role dan hak akses tiap role." />
                    <Link
                        href="/user-management/role/create"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                    >
                        Tambah Role
                    </Link>
                </div>

                <div class="overflow-hidden rounded-2xl border bg-background shadow-sm">
                    <PaginatedTable :headers="headers" :paginator="roles">
                        <template #cell-permissions_count="{ item }">
                            <span class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary">
                                {{ item.permissions?.length ?? 0 }} permission
                            </span>
                        </template>
                        <template #cell-actions="{ item }">
                            <div class="flex items-center gap-3">
                                <Link
                                    :href="`/user-management/role/${item.id}/edit`"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-yellow-100 text-yellow-600 transition hover:bg-yellow-500 hover:text-white"
                                    title="Edit Role"
                                >
                                    <Pencil :size="16" />
                                </Link>
                                <button
                                    @click="deleteRole(item.id)"
                                    type="button"
                                    class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-md bg-red-100 text-red-600 transition hover:bg-red-600 hover:text-white"
                                    title="Hapus Role"
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
