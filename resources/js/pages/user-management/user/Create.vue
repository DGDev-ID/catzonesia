<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    roles: { id: number; name: string }[];
    errors?: Record<string, string>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'User Management', href: '/user-management/user' },
    { title: 'User', href: '/user-management/user' },
    { title: 'Tambah', href: '/user-management/user/create' },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
});

const submit = () => {
    form.post('/user-management/user', { preserveScroll: true });
};
</script>

<template>
    <Head title="Tambah User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Tambah User" description="Buat akun pengguna baru." />
                    <Link href="/user-management/user" class="text-sm text-muted-foreground transition hover:text-foreground">
                        ← Kembali
                    </Link>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <form @submit.prevent="submit" class="space-y-5">

                        <div class="space-y-1">
                            <label class="text-sm font-medium">Nama</label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Nama lengkap"
                                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40"
                                :class="{ 'border-red-400': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium">Email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="email@example.com"
                                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40"
                                :class="{ 'border-red-400': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium">Password</label>
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="Minimal 8 karakter"
                                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40"
                                :class="{ 'border-red-400': form.errors.password }"
                            />
                            <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium">Konfirmasi Password</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="Ulangi password"
                                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40"
                            />
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium">Role</label>
                            <select
                                v-model="form.role_id"
                                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40"
                                :class="{ 'border-red-400': form.errors.role_id }"
                            >
                                <option value="" disabled>Pilih role</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </select>
                            <p v-if="form.errors.role_id" class="text-xs text-red-500">{{ form.errors.role_id }}</p>
                        </div>

                        <div class="flex gap-3 pt-2 justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-lg bg-primary px-5 py-2 text-sm font-medium text-primary-foreground transition hover:opacity-90 disabled:opacity-60"
                            >
                                Simpan User
                            </button>
                            <Link
                                href="/user-management/user"
                                class="rounded-lg border px-5 py-2 text-sm font-medium transition hover:bg-muted"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
