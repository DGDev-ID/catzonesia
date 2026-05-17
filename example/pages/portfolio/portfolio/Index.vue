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

type Portfolio = {
    id: number;
    title: string;
    description: string;
    image: string | null;
    images_count: number;
    link_url: string | null;
    categories?: Array<{ id: number; name: string }>;
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
    portfolios: Paginator<Portfolio>;
    filters: {
        search: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Portfolio', href: '/portfolio' }];

const search = ref(props.filters.search ?? '');
const submitSearch = () => {
    router.get(
        route('portfolio.index'),
        { search: search.value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const headers = [
    { key: 'id', label: 'ID', class: 'w-[80px]' },
    { key: 'image_info', label: 'Gambar', class: 'w-[140px]' },
    { key: 'title', label: 'Judul' },
    { key: 'categories', label: 'Kategori' },
    { key: 'link_url', label: 'Link' },
    { key: 'actions', label: 'Aksi', class: 'w-[140px] text-right' },
];
</script>

<template>
    <Head title="Portfolio" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Portfolio" description="Kelola daftar portfolio dan kategorinya." />
                    <Link
                        :href="route('portfolio.create')"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                    >
                        Tambah Portfolio
                    </Link>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <form class="flex w-full gap-2 sm:max-w-md" @submit.prevent="submitSearch">
                        <Input v-model="search" placeholder="Cari..." class="rounded-xl" />
                        <Button type="submit" variant="outline" class="rounded-xl">Cari</Button>
                    </form>
                </div>

                <PaginatedTable :headers="headers" :paginator="portfolios">
                <template #cell-image_info="{ item }">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 shrink-0 overflow-hidden rounded-md border border-border/50 bg-muted/30">
                            <img v-if="item.image" :src="`/storage/${item.image}`" class="h-full w-full object-cover" alt="thumbnail" />
                            <div v-else class="flex h-full w-full items-center justify-center text-[9px] text-muted-foreground uppercase tracking-tighter">Kosong</div>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-foreground">{{ item.images_count }}</span>
                            <span class="text-[10px] text-muted-foreground">Galeri</span>
                        </div>
                    </div>
                </template>

                <template #cell-categories="{ item }">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="cat in item.categories ?? []"
                            :key="cat.id"
                            class="rounded-full border border-sidebar-border/70 bg-muted/30 px-2 py-0.5 text-xs"
                        >
                            {{ cat.name }}
                        </span>
                    </div>
                </template>

                <template #cell-link_url="{ item }">
                    <a v-if="item.link_url" :href="item.link_url" target="_blank" class="text-sm underline">
                        {{ item.link_url }}
                    </a>
                    <span v-else class="text-sm text-muted-foreground">-</span>
                </template>

                <template #cell-actions="{ item }">
                    <div class="flex justify-end items-center gap-3">
                        <Link
                            :href="route('portfolio.edit', { portfolio: item.id })"
                            class="inline-flex items-center justify-center h-8 w-8 rounded-md bg-yellow-100 text-yellow-600 transition hover:bg-yellow-500 hover:text-white"
                            title="Edit"
                        >
                            <Pencil :size="16" />
                        </Link>
                        <Link
                            :href="route('portfolio.destroy', { portfolio: item.id })"
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
