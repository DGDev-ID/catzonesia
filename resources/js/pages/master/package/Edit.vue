<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    package: any;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Master', href: '/master' },
    { title: 'Package', href: '/master/package' },
    { title: 'Edit', href: `/master/package/edit/${props.package.id}` },
];

const name = ref('');
const price = ref(0);
const isGrooming = ref(false);
const description = ref('');

onMounted(() => {
    name.value = props.package.name;
    price.value = props.package.price;
    isGrooming.value = props.package.is_grooming;
    description.value = props.package.description;
});

const submit = () => {
    router.put(
        `/api/master/package/${props.package.id}`,
        {
            name: name.value,
            price: price.value,
            is_grooming: isGrooming.value,
            description: description.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                router.get('/master/package');
            },
        },
    );
};
</script>

<template>
    <Head title="Edit Package" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="max-w-7xl mx-auto px-6 space-y-8">

                <div class="flex items-center justify-between">
                    <Heading variant="small" title="Edit Package" description="Perbarui data paket layanan yang sudah ada." />
                    <Link href="/master/package" class="text-sm text-muted-foreground hover:text-foreground transition">
                        ← Kembali
                    </Link>
                </div>

                <div class="rounded-2xl border bg-background shadow-sm p-8 max-w-lg">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="mb-2 block text-sm font-medium">Nama Paket</label>
                            <input
                                id="name"
                                v-model="name"
                                type="text"
                                placeholder="Masukkan nama paket"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>

                        <div>
                            <label for="price" class="mb-2 block text-sm font-medium">Harga</label>
                            <input
                                id="price"
                                v-model="price"
                                type="number"
                                placeholder="Masukkan harga"
                                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            />
                        </div>

                        <div>
                            <label for="description" class="mb-2 block text-sm font-medium">Deskripsi</label>
                            <textarea
                                id="description"
                                v-model="description"
                                rows="3"
                                placeholder="Masukkan deskripsi paket"
                                class="flex w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                            ></textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <input
                                id="is_grooming"
                                v-model="isGrooming"
                                type="checkbox"
                                class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                            />
                            <label for="is_grooming" class="text-sm font-medium">Paket Grooming</label>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button
                            type="button"
                            @click="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
                        >
                            Update Package
                        </button>
                        <Link
                            href="/master/package"
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
