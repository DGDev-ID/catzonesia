<script setup lang="ts">
import RoleForm from '@/components/custom/form/RoleForm.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    role: { id: number; name: string };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Role', href: '/role' },
    { title: 'Edit', href: `/role/${props.role.id}/edit` },
];

const form = useForm({
    name: props.role.name,
});

const submit = () => {
    form.put(route('role.update', { role: props.role.id }));
};
</script>

<template>
    <Head title="Edit Role" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Edit Role" description="Ubah data role." />
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('role.index')">Kembali</Link>
                    </Button>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <RoleForm v-model="form" submit-label="Update" @submit="submit" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
