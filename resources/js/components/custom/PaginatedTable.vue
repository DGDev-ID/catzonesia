<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

type TableHeader = {
    key: string;
    label: string;
    class?: string;
    suffix?: string;
    suffixKey?: string;
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
        paginator: Paginator<any> | null | undefined;
        emptyText?: string;
    }>(),
    {
        emptyText: 'Tidak ada data',
    },
);

const safePaginator = computed<Paginator<any>>(() => {
    if (!props.paginator) {
        return { 
            data: [], 
            links: [], 
            meta: { 
                current_page: 1, 
                from: null, 
                last_page: 1, 
                links: [], 
                path: '', 
                per_page: 10, 
                to: null, 
                total: 0 
            } 
        };
    }
    return props.paginator;
});

const hasData = computed(() => (safePaginator.value.data?.length ?? 0) > 0);

const links = computed(() => {
    const raw = safePaginator.value.links ?? safePaginator.value.meta?.links ?? [];
    return raw as PaginatorLink[];
});

const meta = computed<PaginatorMeta>(() => {
    const p: any = safePaginator.value ?? {};
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

const formatIsoDateToGmtPlus7 = (value: string) => {
    const match = value.match(
        /^(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(?:\.\d+)?(Z|[+-]\d{2}:\d{2})$/,
    );

    if (!match) return value;

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;

    const formatted = new Intl.DateTimeFormat('sv-SE', {
        timeZone: 'Asia/Jakarta',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    }).format(date);

    return `${formatted} GMT+7`;
};

const getCellValue = (item: Record<string, any> | null | undefined, key: string) => {
    if (!item) return '';

    // Handle nested properties like "baseUnit.name"
    const keys = key.split('.');
    let value = item;

    for (const k of keys) {
        if (value && typeof value === 'object' && k in value) {
            value = value[k];
        } else {
            return '';
        }
    }

    if (typeof value === 'string') {
        return formatIsoDateToGmtPlus7(value);
    }

    return value;
};

const getCellDisplayValue = (item: Record<string, any> | null | undefined, header: TableHeader) => {
    const raw = getCellValue(item, header.key);
    if (raw === '') return '';

    const suffixFromKey = header.suffixKey ? getCellValue(item, header.suffixKey) : '';
    const suffix = header.suffix ?? (typeof suffixFromKey === 'string' ? suffixFromKey : suffixFromKey !== '' ? String(suffixFromKey) : '');

    return suffix ? `${String(raw)} ${suffix}` : raw;
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

                    <tr v-for="(item, rowIndex) in safePaginator.data" :key="item?.id ?? rowIndex" class="border-t transition hover:bg-muted/40">
                        <td v-for="header in headers" :key="header.key" class="px-6 py-4 align-top">
                            <slot :name="`cell-${header.key}`" :item="item">
                                <template v-if="header.key === 'id'">{{ getRowNumber(rowIndex) }}</template>
                                <template v-else>{{ getCellDisplayValue(item, header) }}</template>
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
