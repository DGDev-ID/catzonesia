<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

type TableHeader = {
    key: string;
    label: string;
    class?: string;
};

type PaginatorLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type PaginatorMeta = {
    current_page: number;
    from: number | null;
    last_page: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    path: string;
    per_page: number;
    to: number | null;
    total: number;
};

type Paginator<T> = {
    data: T[];
    links: PaginatorLink[];
    meta?: PaginatorMeta;
    current_page?: number;
    from?: number | null;
    last_page?: number;
    path?: string;
    per_page?: number;
    to?: number | null;
    total?: number;
};

const props = withDefaults(
    defineProps<{
        headers: TableHeader[];
        paginator: Paginator<any>;
        emptyText?: string;
    }>(),
    {
        emptyText: 'Tidak ada data',
    },
);

const hasData = computed(() => (props.paginator?.data?.length ?? 0) > 0);

const links = computed(() => {
    const raw = props.paginator.links ?? props.paginator.meta?.links ?? [];
    return raw as PaginatorLink[];
});

const meta = computed<PaginatorMeta>(() => {
    const p: any = props.paginator ?? {};
    const m: any = p.meta ?? p;

    return {
        current_page: m.current_page ?? 1,
        from: m.from ?? null,
        last_page: m.last_page ?? 1,
        links: (m.links ?? []) as PaginatorMeta['links'],
        path: m.path ?? '',
        per_page: m.per_page ?? 10,
        to: m.to ?? null,
        total: m.total ?? (Array.isArray(p.data) ? p.data.length : 0),
    };
});

const getRowNumber = (rowIndex: number) => {
    return (meta.value.from ?? 1) + rowIndex;
};

const getCellValue = (item: Record<string, any> | null | undefined, key: string) => {
    if (!item) return '';
    if (key in item) return item[key];
    return '';
};
</script>

<template>
    <div class="w-full rounded-2xl border bg-background shadow-sm">
        <div class="overflow-auto rounded-2xl">
            <table class="min-w-full text-sm">
                <thead class="bg-muted/50">
                    <tr class="text-muted-foreground">
                        <th v-for="header in headers" :key="header.key" scope="col" class="px-6 py-4 text-left font-medium" :class="header.class">
                            {{ header.key === 'id' ? 'No' : header.label }}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="!hasData">
                        <td :colspan="headers.length" class="px-6 py-10 text-center text-muted-foreground">
                            {{ emptyText }}
                        </td>
                    </tr>

                    <tr v-for="(item, rowIndex) in paginator.data" :key="item?.id ?? rowIndex" class="border-t transition hover:bg-muted/40">
                        <td v-for="header in headers" :key="header.key" class="px-6 py-4 align-top">
                            <slot :name="`cell-${header.key}`" :item="item">
                                <template v-if="header.key === 'id'">{{ getRowNumber(rowIndex) }}</template>
                                <template v-else>{{ getCellValue(item, header.key) }}</template>
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t bg-background p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-muted-foreground">
                <template v-if="meta.from !== null && meta.to !== null"> Menampilkan {{ meta.from }} - {{ meta.to }} dari {{ meta.total }} </template>
                <template v-else>Menampilkan 0 dari {{ meta.total }}</template>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <template v-for="(link, index) in links" :key="index">
                    <Button v-if="link.url === null" variant="outline" size="sm" :disabled="true" class="rounded-xl">
                        <span v-html="link.label" />
                    </Button>
                    <Button v-else as-child :variant="link.active ? 'default' : 'outline'" size="sm" class="rounded-xl">
                        <Link :href="link.url" preserve-scroll preserve-state>
                            <span v-html="link.label" />
                        </Link>
                    </Button>
                </template>
            </div>
        </div>
    </div>
</template>
