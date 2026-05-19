<script setup lang="ts">
import { Checkbox } from '@/components/ui/checkbox';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Lock, Mail } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="flex min-h-screen">
        <!-- Left Panel -->
        <div class="relative hidden flex-col lg:flex lg:w-1/2">
            <!-- Background: dark navy gradient + subtle batik dot pattern -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#0f1e3d] via-[#1a2f5a] to-[#0d1a33]" />
            <div
                class="absolute inset-0 opacity-10"
                style="background-image: radial-gradient(circle, #c9a840 1.5px, transparent 1.5px); background-size: 28px 28px"
            />
            <!-- Decorative circle (batik accent) -->
            <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full border border-[#8b1a1a]/30 opacity-40" />
            <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full border border-[#c9a840]/20 opacity-30" />

            <!-- Brand top-left -->
            <div class="relative z-10 flex items-center gap-3 p-8">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#8b1a1a]">
                    <img src="/logo.png" alt="Catzonesia" class="h-7 w-7 object-contain brightness-0 invert" />
                </div>
                <span class="text-xs font-bold tracking-[0.2em] text-white uppercase">Catzonesia</span>
            </div>

            <!-- Bottom text -->
            <div class="relative z-10 mt-auto p-8 pb-12">
                <h1 class="text-4xl font-bold leading-tight text-white">
                    Dashboard<br />Management System
                </h1>
                <p class="mt-3 text-sm leading-relaxed text-slate-400">
                    Kelola operasional bisnis kucing Anda<br />dengan mudah dan efisien melalui<br />dashboard yang terintegrasi.
                </p>
                <!-- Decorative dots/line -->
                <div class="mt-6 flex items-center gap-2">
                    <div class="h-0.5 w-8 bg-[#8b1a1a]" />
                    <div class="h-2 w-2 rounded-full bg-[#c9a840]" />
                    <div class="h-0.5 w-8 bg-slate-700" />
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="flex w-full items-center justify-center bg-white px-8 py-12 lg:w-1/2">
            <div class="w-full max-w-sm">
                <!-- Logo -->
                <div class="mb-6 flex justify-center">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-red-50 bg-red-50 shadow-md">
                        <img src="/logo.png" alt="Catzonesia" class="h-14 w-14 object-contain" />
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-bold text-neutral-800">Selamat Datang</h2>
                    <p class="mt-1 text-sm text-neutral-500">Masukkan kredensial Anda untuk masuk</p>
                </div>

                <!-- Status -->
                <div v-if="status" class="mb-4 rounded-lg bg-green-50 p-3 text-center text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-neutral-700">Alamat Email</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                                <Mail class="h-4 w-4 text-neutral-400" />
                            </div>
                            <input
                                id="email"
                                type="email"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="email@example.com"
                                class="w-full rounded-lg border border-neutral-300 py-2.5 pr-4 pl-10 text-sm focus:ring-2 focus:ring-[#8b1a1a]/40 focus:border-[#8b1a1a] focus:outline-none"
                                :class="{ 'border-red-400': form.errors.email }"
                            />
                        </div>
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-neutral-700">Password</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                                <Lock class="h-4 w-4 text-neutral-400" />
                            </div>
                            <input
                                id="password"
                                type="password"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="Password"
                                class="w-full rounded-lg border border-neutral-300 py-2.5 pr-4 pl-10 text-sm focus:ring-2 focus:ring-[#8b1a1a]/40 focus:border-[#8b1a1a] focus:outline-none"
                                :class="{ 'border-red-400': form.errors.password }"
                            />
                        </div>
                        <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember me -->
                    <div class="flex items-center gap-2">
                        <Checkbox id="remember" v-model:checked="form.remember" />
                        <label for="remember" class="cursor-pointer text-sm text-neutral-600">Ingat saya</label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-[#8b1a1a] py-2.5 font-semibold text-white transition-colors hover:bg-[#6e1515] disabled:opacity-60"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Masuk ke Dashboard
                    </button>
                </form>

                <!-- Footer -->
                <p class="mt-8 text-center text-xs text-neutral-400">© 2026 Catzonesia. All rights reserved.</p>
            </div>
        </div>
    </div>
</template>
