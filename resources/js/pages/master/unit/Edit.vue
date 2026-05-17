<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    unit: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Unit', href: '/master/unit' },
    { title: 'Edit', href: `/master/unit/edit/${props.unit.id}` },
];

const name = ref(props.unit.name);

const handleSubmit = () => {
    if (!name.value.trim()) {
        alert('Please enter a unit name');
        return;
    }
    router.put(`/api/master/unit/${props.unit.id}`, { name: name.value.trim() });
};
</script>

<template>
    <Head title="Edit Unit" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Edit Unit" description="Perbarui data unit yang sudah ada." />
                    <Link href="/master/unit" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <div class="rounded-2xl border bg-background shadow-sm p-8 max-w-md">
                    <div class="mb-6">
                        <label for="name" class="mb-2 block text-sm font-medium">Nama Unit</label>
                        <input
                            id="name"
                            v-model="name"
                            type="text"
                            placeholder="Masukkan nama unit"
                            class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        />
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="handleSubmit"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                        >
                            Update Unit
                        </button>
                        <Link
                            href="/master/unit"
                            class="inline-flex items-center justify-center rounded-xl border border-input bg-background px-5 py-2.5 text-sm font-medium text-foreground shadow-sm transition hover:bg-muted"
                        >
                            Batal
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
