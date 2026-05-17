<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
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
    <!-- Full-page background with Joyze blobs -->
    <div class="relative min-h-screen flex items-center justify-center bg-white overflow-hidden px-4">

        <!-- Background blobs (Joyze theme) -->
        <span class="absolute top-0 left-0 w-96 h-96 rounded-full bg-[#FFF183] opacity-50 blur-3xl -translate-x-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true" />
        <span class="absolute bottom-0 right-0 w-80 h-80 rounded-full bg-[#FFAD33] opacity-20 blur-3xl translate-x-1/3 translate-y-1/3 pointer-events-none" aria-hidden="true" />
        <span class="absolute top-1/2 left-1/3 w-64 h-64 rounded-full bg-[#FFF183] opacity-20 blur-3xl pointer-events-none" aria-hidden="true" />

        <!-- Card -->
        <div class="relative z-10 w-full max-w-md">

            <!-- Logo & Heading -->
            <div class="flex flex-col items-center mb-8">
                <div class="mb-4">
                    <img src="/logo-joyze.svg" alt="Joyze Creative" class="h-12 w-auto" />
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Welcome back!</h1>
                <p class="mt-1 text-sm text-gray-500">Sign in to your Joyze Hub account</p>
            </div>

            <!-- Status message -->
            <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600 bg-green-50 rounded-xl py-2 px-4">
                {{ status }}
            </div>

            <!-- Form card -->
            <div class="bg-white border border-gray-100 shadow-xl rounded-2xl px-8 py-8">
                <form @submit.prevent="submit" class="flex flex-col gap-5">

                    <!-- Email -->
                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-sm font-medium text-gray-700">Email address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                                <Mail class="h-4 w-4" />
                            </span>
                            <input
                                id="email"
                                type="email"
                                required
                                autofocus
                                tabindex="1"
                                autocomplete="email"
                                v-model="form.email"
                                placeholder="email@example.com"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFAD33] focus:border-transparent transition"
                            />
                        </div>
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                                <Lock class="h-4 w-4" />
                            </span>
                            <input
                                id="password"
                                type="password"
                                required
                                tabindex="2"
                                autocomplete="current-password"
                                v-model="form.password"
                                placeholder="Password"
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#FFAD33] focus:border-transparent transition"
                            />
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        tabindex="3"
                        :disabled="form.processing"
                        class="mt-2 w-full flex items-center justify-center gap-2 bg-[#FFAD33] hover:opacity-90 text-white font-semibold text-sm py-3 px-6 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        <span>{{ form.processing ? 'Signing in...' : 'Sign In' }}</span>
                    </button>

                </form>
            </div>

            <!-- Footer branding -->
            <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ new Date().getFullYear() }} Joyze Creative Agency. All rights reserved.</p>

        </div>
    </div>
</template>
