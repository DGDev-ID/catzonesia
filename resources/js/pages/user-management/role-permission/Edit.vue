<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    role: { id: number; name: string; permissions: { name: string }[] };
    permissions: Record<string, string[]>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/user-management/user' },
    { title: 'Role & Permission', href: '/user-management/role' },
    { title: 'Edit', href: '#' },
];

const moduleLabels: Record<string, string> = {
    'dashboard': 'Dashboard',
    'master.category': 'Master — Category',
    'master.unit': 'Master — Unit',
    'master.product': 'Master — Product',
    'master.package': 'Master — Package',
    'product-movement': 'Product Movement',
    'transaction': 'Transaction',
    'cashier': 'Cashier',
    'user-management.user': 'User Management — User',
    'user-management.role': 'User Management — Role',
};

const form = useForm({
    name: props.role.name,
    permissions: props.role.permissions.map((p) => p.name),
});

const toggleAll = (module: string, perms: string[]) => {
    const allChecked = perms.every((p) => form.permissions.includes(p));
    if (allChecked) {
        form.permissions = form.permissions.filter((p) => !perms.includes(p));
    } else {
        const toAdd = perms.filter((p) => !form.permissions.includes(p));
        form.permissions = [...form.permissions, ...toAdd];
    }
};

const isAllChecked = (perms: string[]) => perms.every((p) => form.permissions.includes(p));
const isIndeterminate = (perms: string[]) => !isAllChecked(perms) && perms.some((p) => form.permissions.includes(p));

const submit = () => {
    form.put(`/user-management/role/${props.role.id}`, { preserveScroll: true });
};
</script>

<template>
    <Head title="Edit Role" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Edit Role" description="Ubah nama dan hak akses role." />
                    <Link href="/user-management/role" class="text-sm text-muted-foreground transition hover:text-foreground">
                        ← Kembali
                    </Link>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Role Name -->
                    <div class="rounded-2xl border bg-background p-6 shadow-sm">
                        <div class="max-w-sm space-y-1">
                            <label class="text-sm font-medium">Nama Role</label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="contoh: admin, kasir, manajer"
                                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40"
                                :class="{ 'border-red-400': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="rounded-2xl border bg-background p-6 shadow-sm">
                        <h3 class="mb-4 text-sm font-semibold">Hak Akses (Permission)</h3>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div
                                v-for="(perms, module) in permissions"
                                :key="module"
                                class="rounded-xl border p-4"
                            >
                                <label class="mb-3 flex cursor-pointer items-center gap-2">
                                    <input
                                        type="checkbox"
                                        :checked="isAllChecked(perms)"
                                        :indeterminate="isIndeterminate(perms)"
                                        @change="toggleAll(String(module), perms)"
                                        class="h-4 w-4 rounded border-gray-300 accent-primary"
                                    />
                                    <span class="text-sm font-semibold">{{ moduleLabels[module] ?? module }}</span>
                                </label>

                                <div class="space-y-2 pl-1">
                                    <label
                                        v-for="perm in perms"
                                        :key="perm"
                                        class="flex cursor-pointer items-center gap-2"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="perm"
                                            v-model="form.permissions"
                                            class="h-4 w-4 rounded border-gray-300 accent-primary"
                                        />
                                        <span class="text-xs text-muted-foreground">
                                            {{ perm.split('.').pop() }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <p v-if="form.errors.permissions" class="mt-2 text-xs text-red-500">{{ form.errors.permissions }}</p>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-lg bg-primary px-5 py-2 text-sm font-medium text-primary-foreground transition hover:opacity-90 disabled:opacity-60"
                        >
                            Update Role
                        </button>
                        <Link
                            href="/user-management/role"
                            class="rounded-lg border px-5 py-2 text-sm font-medium transition hover:bg-muted"
                        >
                            Batal
                        </Link>
                    </div>
                </form>

            </div>
        </div>
    </AppLayout>
</template>
