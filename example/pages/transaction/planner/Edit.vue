<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type PlannerItem = {
    id: number;
    parent_id: number | null;
    description: string;
    attachment_url: string | null;
    status: 'pending' | 'done' | 'revision';
    children: PlannerItem[];
};

const props = defineProps<{
    transaction: {
        id: number;
        ticket_id: string;
        cust_name: string;
        cust_email: string;
        cust_phone: string;
        start_date: string;
        end_date: string;
        total_price: string | number;
        is_ticket_closed: boolean;
        service: { title: string | null };
        service_package: { title: string | null; type: string | null; days: number | null };
    };
    planners: PlannerItem[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Planner', href: '/transaction/planner' },
    { title: props.transaction.ticket_id, href: `/transaction/planner/${props.transaction.id}/edit` },
];

const closeForm = useForm({});
const closeTicket = () => {
    closeForm.put(route('transaction.planner.close', { transaction: props.transaction.id }), { preserveScroll: true });
};

const createForm = useForm({
    parent_id: null as number | null,
    description: '',
    attachment_url: '',
    status: 'pending' as 'pending' | 'done' | 'revision',
});

const submitCreate = () => {
    createForm.post(route('transaction.planner.items.store', { transaction: props.transaction.id }), {
        preserveScroll: true,
        onSuccess: () => createForm.reset('parent_id', 'description', 'attachment_url', 'status'),
    });
};

const statusBadgeClass = (status: PlannerItem['status']) => {
    if (status === 'done') return 'border-emerald-200 bg-emerald-50 text-emerald-800';
    if (status === 'revision') return 'border-amber-200 bg-amber-50 text-amber-800';
    return 'border-sidebar-border/70 bg-muted/30 text-foreground';
};

const flattened = computed(() => {
    const out: PlannerItem[] = [];
    const walk = (nodes: PlannerItem[]) => {
        for (const n of nodes) {
            out.push(n);
            walk(n.children ?? []);
        }
    };
    walk(props.planners ?? []);
    return out;
});

const editingId = ref<number | null>(null);
const editForm = useForm({
    description: '',
    attachment_url: '',
    status: 'pending' as 'pending' | 'done' | 'revision',
});

const startEdit = (item: PlannerItem) => {
    editingId.value = item.id;
    editForm.setDefaults({
        description: item.description,
        attachment_url: item.attachment_url ?? '',
        status: item.status,
    });
    editForm.reset();
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
    editForm.clearErrors();
};

const submitEdit = (itemId: number) => {
    editForm.put(route('transaction.planner.items.update', { transaction: props.transaction.id, planner: itemId }), {
        preserveScroll: true,
        onSuccess: cancelEdit,
    });
};

const createChildFor = ref<number | null>(null);
const childForm = useForm({
    description: '',
    attachment_url: '',
    status: 'pending' as 'pending' | 'done' | 'revision',
});

const startCreateChild = (parentId: number) => {
    createChildFor.value = parentId;
    childForm.reset();
    childForm.clearErrors();
};

const cancelCreateChild = () => {
    createChildFor.value = null;
    childForm.reset();
    childForm.clearErrors();
};

const submitCreateChild = (parentId: number) => {
    childForm.post(
        route('transaction.planner.items.store', { transaction: props.transaction.id }),
        {
            preserveScroll: true,
            data: {
                parent_id: parentId,
                description: childForm.description,
                attachment_url: childForm.attachment_url || null,
                status: childForm.status,
            },
            onSuccess: cancelCreateChild,
        },
    );
};

const renderNodes = (nodes: PlannerItem[], level = 0): PlannerItem[] =>
    nodes.flatMap((n) => [({ ...n, level } as any), ...renderNodes(n.children ?? [], level + 1)]);

const rows = computed(() => renderNodes(props.planners ?? []));
</script>

<template>
    <Head title="Edit Planner" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall
                        title="Planner"
                        :description="`Ticket ${transaction.ticket_id} • ${transaction.service.title ?? '-'} • ${transaction.service_package.title ?? '-'}`"
                    />
                    <div class="flex items-center gap-2">
                        <Button as-child variant="outline" class="rounded-xl">
                            <Link :href="route('transaction.planner.index')">Kembali</Link>
                        </Button>
                        <Button
                            type="button"
                            variant="destructive"
                            class="rounded-xl"
                            :disabled="closeForm.processing"
                            @click="closeTicket"
                        >
                            Tutup Ticket
                        </Button>
                    </div>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <div class="grid gap-4 md:grid-cols-12">
                        <div class="md:col-span-4">
                            <div class="text-xs text-muted-foreground">Customer</div>
                            <div class="font-medium">{{ transaction.cust_name }}</div>
                            <div class="text-xs text-muted-foreground">{{ transaction.cust_email }} | {{ transaction.cust_phone }}</div>
                        </div>
                        <div class="md:col-span-4">
                            <div class="text-xs text-muted-foreground">Tanggal</div>
                            <div class="font-medium">{{ transaction.start_date }} - {{ transaction.end_date }}</div>
                        </div>
                        <div class="md:col-span-4">
                            <div class="text-xs text-muted-foreground">Paket</div>
                            <div class="font-medium">
                                {{ transaction.service_package.title ?? '-' }}
                            </div>
                            <div class="text-xs text-muted-foreground">
                                Type: {{ transaction.service_package.type ?? '-' }} | Days: {{ transaction.service_package.days ?? 7 }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <h2 class="mb-4 text-base font-semibold">Tambah Plan</h2>
                    <form class="grid gap-3 md:grid-cols-12" @submit.prevent="submitCreate">
                        <div class="md:col-span-12">
                            <Label for="plan_description">Deskripsi</Label>
                            <textarea
                                id="plan_description"
                                v-model="createForm.description"
                                class="flex min-h-[96px] w-full rounded-xl border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                placeholder="Tulis plan..."
                            />
                            <InputError :message="createForm.errors.description" />
                        </div>

                        <div class="md:col-span-8">
                            <Label for="plan_attachment">Attachment URL (opsional)</Label>
                            <Input id="plan_attachment" v-model="createForm.attachment_url" class="rounded-xl" placeholder="https://..." />
                            <InputError :message="createForm.errors.attachment_url" />
                        </div>

                        <div class="md:col-span-4">
                            <Label for="plan_status">Status</Label>
                            <select
                                id="plan_status"
                                v-model="createForm.status"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            >
                                <option value="pending">pending</option>
                                <option value="done">done</option>
                                <option value="revision">revision</option>
                            </select>
                            <InputError :message="createForm.errors.status" />
                        </div>

                        <div class="md:col-span-12">
                            <Button type="submit" :disabled="createForm.processing">Tambah</Button>
                        </div>
                    </form>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <h2 class="text-base font-semibold">List Plan</h2>
                        <div class="text-xs text-muted-foreground">{{ flattened.length }} item</div>
                    </div>

                    <div v-if="rows.length === 0" class="text-sm text-muted-foreground">Belum ada plan.</div>

                    <div v-else class="space-y-3">
                        <div
                            v-for="item in rows"
                            :key="item.id"
                            class="rounded-2xl border border-sidebar-border/70 p-4"
                            :style="{ marginLeft: `${(item as any).level * 16}px` }"
                        >
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div class="flex-1 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full border px-2 py-0.5 text-xs" :class="statusBadgeClass(item.status)">
                                            {{ item.status }}
                                        </span>
                                        <span class="text-xs text-muted-foreground">#{{ item.id }}</span>
                                    </div>

                                    <template v-if="editingId === item.id">
                                        <div class="grid gap-3">
                                            <div class="grid gap-2">
                                                <Label>Deskripsi</Label>
                                                <textarea
                                                    v-model="editForm.description"
                                                    class="flex min-h-[96px] w-full rounded-xl border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                                />
                                                <InputError :message="editForm.errors.description" />
                                            </div>

                                            <div class="grid gap-2">
                                                <Label>Attachment URL</Label>
                                                <Input v-model="editForm.attachment_url" class="rounded-xl" placeholder="https://..." />
                                                <InputError :message="editForm.errors.attachment_url" />
                                            </div>

                                            <div class="grid gap-2">
                                                <Label>Status</Label>
                                                <select
                                                    v-model="editForm.status"
                                                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                                >
                                                    <option value="pending">pending</option>
                                                    <option value="done">done</option>
                                                    <option value="revision">revision</option>
                                                </select>
                                                <InputError :message="editForm.errors.status" />
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="whitespace-pre-line text-sm">{{ item.description }}</div>
                                        <div v-if="item.attachment_url" class="text-xs">
                                            <a
                                                class="text-primary underline underline-offset-4"
                                                :href="item.attachment_url"
                                                target="_blank"
                                                rel="noreferrer"
                                            >
                                                {{ item.attachment_url }}
                                            </a>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex items-center gap-2">
                                    <template v-if="editingId === item.id">
                                        <Button type="button" size="sm" @click="submitEdit(item.id)" :disabled="editForm.processing">Simpan</Button>
                                        <Button type="button" variant="outline" size="sm" @click="cancelEdit">Batal</Button>
                                    </template>
                                    <template v-else>
                                        <Button type="button" variant="outline" size="sm" @click="startEdit(item)">Edit</Button>
                                        <Button type="button" variant="outline" size="sm" @click="startCreateChild(item.id)">Sub</Button>
                                        <Button as-child variant="destructive" size="sm">
                                            <Link
                                                :href="route('transaction.planner.items.destroy', { transaction: transaction.id, planner: item.id })"
                                                method="delete"
                                                as="button"
                                                preserve-scroll
                                            >
                                                Hapus
                                            </Link>
                                        </Button>
                                    </template>
                                </div>
                            </div>

                            <div v-if="createChildFor === item.id" class="mt-4 rounded-xl border border-sidebar-border/70 bg-muted/20 p-4">
                                <div class="mb-2 text-sm font-medium">Tambah Sub Plan</div>
                                <form class="grid gap-3 md:grid-cols-12" @submit.prevent="submitCreateChild(item.id)">
                                    <div class="md:col-span-12">
                                        <Label>Deskripsi</Label>
                                        <textarea
                                            v-model="childForm.description"
                                            class="flex min-h-[96px] w-full rounded-xl border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                        />
                                        <InputError :message="childForm.errors.description" />
                                    </div>
                                    <div class="md:col-span-8">
                                        <Label>Attachment URL</Label>
                                        <Input v-model="childForm.attachment_url" class="rounded-xl" placeholder="https://..." />
                                        <InputError :message="childForm.errors.attachment_url" />
                                    </div>
                                    <div class="md:col-span-4">
                                        <Label>Status</Label>
                                        <select
                                            v-model="childForm.status"
                                            class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                        >
                                            <option value="pending">pending</option>
                                            <option value="done">done</option>
                                            <option value="revision">revision</option>
                                        </select>
                                        <InputError :message="childForm.errors.status" />
                                    </div>
                                    <div class="flex items-center gap-2 md:col-span-12">
                                        <Button type="submit" :disabled="childForm.processing">Tambah</Button>
                                        <Button type="button" variant="outline" class="rounded-xl" @click="cancelCreateChild">Batal</Button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
