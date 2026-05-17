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

type Service = {
    id: number;
    title: string;
    description: string;
    packages_count?: number;
    categories?: Array<{ id: number; title: string }>;
};

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
    services: Paginator<Service>;
    filters: { search: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Service', href: '/service' }];

const search = ref(props.filters.search ?? '');
const submitSearch = () => {
    router.get(
        route('service.index'),
        { search: search.value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const headers = [
    { key: 'id', label: 'ID', class: 'w-[80px]' },
    { key: 'title', label: 'Judul' },
    { key: 'categories', label: 'Kategori' },
    { key: 'packages_count', label: 'Paket', class: 'w-[100px]' },
    { key: 'actions', label: 'Aksi', class: 'w-[200px]' },
];
</script>

<template>
    <Head title="Service" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Services" description="Kelola daftar service, kategori, dan paketnya." />
                    <Link
                        :href="route('service.create')"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                    >
                        Tambah Service
                    </Link>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <form class="flex w-full gap-2 sm:max-w-md" @submit.prevent="submitSearch">
                        <Input v-model="search" placeholder="Cari..." class="rounded-xl" />
                        <Button type="submit" variant="outline" class="rounded-xl">Cari</Button>
                    </form>
                </div>

                <PaginatedTable :headers="headers" :paginator="services">
                <template #cell-categories="{ item }">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="cat in item.categories ?? []"
                            :key="cat.id"
                            class="rounded-full border border-sidebar-border/70 bg-muted/30 px-2 py-0.5 text-xs"
                        >
                            {{ cat.title }}
                        </span>
                    </div>
                </template>

                <template #cell-actions="{ item }">
                    <div class="flex justify-end items-center gap-3">
                        <Link
                            :href="route('service.edit', { service: item.id })"
                            class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-yellow-100 text-yellow-600 transition hover:bg-yellow-500 hover:text-white"
                            title="Edit"
                        >
                            <Pencil :size="16" />
                        </Link>
                        <Link
                            :href="route('service.destroy', { service: item.id })"
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
