<script setup lang="ts">
import PaginatedTable from '@/components/custom/PaginatedTable.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

type Role = {
    id: number;
    name: string;
    permissions?: Array<{ id: number; name: string }>;
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
    roles: Paginator<Role>;
    filters: { search: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Role Permission', href: '/permission' }];

const search = ref(props.filters.search ?? '');
const submitSearch = () => {
    router.get(
        route('permission.index'),
        { search: search.value },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const headers = [
    { key: 'name', label: 'Role' },
    { key: 'permissions', label: 'Permissions' },
    { key: 'actions', label: 'Aksi', class: 'w-[160px]' },
];
</script>

<template>
    <Head title="Role Permission" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Role Permission" description="Lihat role dan plotting permissions." />
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <form class="flex w-full gap-2 sm:max-w-md" @submit.prevent="submitSearch">
                        <Input v-model="search" placeholder="Cari role..." class="rounded-xl" />
                        <Button type="submit" variant="outline" class="rounded-xl">Cari</Button>
                    </form>
                </div>

                <PaginatedTable :headers="headers" :paginator="roles">
                <template #cell-permissions="{ item }">
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="perm in item.permissions ?? []"
                            :key="perm.id"
                            class="rounded-full border border-sidebar-border/70 bg-muted/30 px-2 py-0.5 text-xs"
                        >
                            {{ perm.name }}
                        </span>
                        <span v-if="(item.permissions ?? []).length === 0" class="text-sm text-muted-foreground">-</span>
                    </div>
                </template>

                <template #cell-actions="{ item }">
                    <Link
                        :href="route('permission.edit', { role: item.id })"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                    >
                        Plotting
                    </Link>
                </template>
            </PaginatedTable>
            </div>
        </div>
    </AppLayout>
</template>
