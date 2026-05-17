<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

type Role = { id: number; name: string; guard_name: string };

type Paginator<T> = {
    data: T[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        per_page: number;
        to: number | null;
        total: number;
    };
};

const props = defineProps<{
    roles: Paginator<Role>;
    filters: { search: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Role', href: '/role' }];

const search = ref(props.filters.search ?? '');
const submitSearch = () => {
    router.get(
        route('role.index'),
        { search: search.value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const headers = [
    { key: 'id', label: 'ID', class: 'w-[80px]' },
    { key: 'name', label: 'Nama' },
    { key: 'guard_name', label: 'Guard', class: 'w-[120px]' },
    { key: 'actions', label: 'Aksi', class: 'w-[200px]' },
];
</script>

<template>
    <Head title="Role" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Role" description="Kelola daftar role." />
                    <Link
                        :href="route('role.create')"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                    >
                        Tambah Role
                    </Link>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <form class="flex w-full gap-2 sm:max-w-md" @submit.prevent="submitSearch">
                        <Input v-model="search" placeholder="Cari..." class="rounded-xl" />
                        <Button type="submit" variant="outline" class="rounded-xl">Cari</Button>
                    </form>
                </div>

                <PaginatedTable :headers="headers" :paginator="roles">
                    <template #cell-actions="{ item }">
                        <div class="flex justify-end items-center gap-3">
                            <Link
                                :href="route('role.edit', { role: item.id })"
                                class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-yellow-100 text-yellow-600 transition hover:bg-yellow-500 hover:text-white"
                                title="Edit"
                            >
                                <Pencil :size="16" />
                            </Link>
                            <Link
                                :href="route('role.destroy', { role: item.id })"
                                method="delete"
                                as="button"
                                preserve-scroll
                                class="inline-flex cursor-pointer items-center justify-center h-8 w-8 rounded-md bg-red-100 text-red-600 transition hover:bg-red-600 hover:text-white"
                                title="Hapus"
                            >
                                <Trash2 :size="16" />
                            </Link>
                        </div>
                    </template>
                </PaginatedTable>
            </div>
        </div>
    </AppLayout>
</template>
