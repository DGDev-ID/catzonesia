<script setup lang="ts">
import RolePermissionForm from '@/components/custom/form/RolePermissionForm.vue';
import type { SearchableOption } from '@/components/custom/SearchableInputSelect.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    role: { id: number; name: string };
    permissions: Array<{ id: number; name: string }>;
    selectedPermissionNames: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Role Permission', href: '/permission' },
    { title: 'Edit', href: `/permission/${props.role.id}/edit` },
];

const permissionOptions = computed<SearchableOption[]>(() =>
    props.permissions.map((p) => ({
        label: p.name,
        value: p.name,
    })),
);

const form = useForm({
    permission_names: props.selectedPermissionNames ?? [],
});

const submit = () => {
    form.put(route('permission.update', { role: props.role.id }));
};
</script>

<template>
    <Head title="Edit Role Permission" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Plotting Permission" :description="`Role: ${role.name}`" />
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('permission.index')">Kembali</Link>
                    </Button>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <RolePermissionForm v-model="form" :permission-options="permissionOptions" submit-label="Simpan" @submit="submit" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
