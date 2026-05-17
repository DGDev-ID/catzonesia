<script setup lang="ts">
import ServiceForm from '@/components/custom/form/ServiceForm.vue';
import type { SearchableOption } from '@/components/custom/SearchableInputSelect.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

type PackageDescription = {
    id: number;
    title: string;
};

type ServicePackage = {
    id: number;
    title: string;
    price: number | string;
    type: string | null;
    days: number;
    descriptions: PackageDescription[];
};

const props = defineProps<{
    service: {
        id: number;
        title: string;
        description: string;
        is_timeline?: boolean;
    };
    categories: Array<{ id: number; title: string }>;
    selectedCategoryIds: number[];
    packages: ServicePackage[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Service', href: '/service' },
    { title: 'Edit', href: `/service/${props.service.id}/edit` },
];

const categoryOptions = computed<SearchableOption[]>(() =>
    props.categories.map((c) => ({
        label: c.title,
        value: c.id,
    })),
);

const serviceForm = useForm({
    title: props.service.title,
    description: props.service.description,
    is_timeline: props.service.is_timeline ?? true,
    category_ids: props.selectedCategoryIds ?? [],
});

const submitService = () => {
    serviceForm.put(route('service.update', { service: props.service.id }), { preserveScroll: true });
};

const packageCreateForm = useForm({
    title: '',
    price: '',
    type: '',
    days: 7,
});

const createPackage = () => {
    packageCreateForm.post(route('service.packages.store', { service: props.service.id }), {
        preserveScroll: true,
        onSuccess: () => packageCreateForm.reset(),
    });
};

const editingPackageId = ref<number | null>(null);
const packageEditForm = useForm({
    title: '',
    price: '',
    type: '',
    days: 7,
});

const startEditPackage = (pkg: ServicePackage) => {
    editingPackageId.value = pkg.id;
    packageEditForm.setDefaults({
        title: pkg.title,
        price: String(pkg.price),
        type: pkg.type ?? '',
        days: pkg.days ?? 7,
    });
    packageEditForm.reset();
};

const cancelEditPackage = () => {
    editingPackageId.value = null;
    packageEditForm.reset();
    packageEditForm.clearErrors();
};

const updatePackage = () => {
    if (editingPackageId.value === null) return;
    packageEditForm.put(
        route('service.packages.update', { service: props.service.id, package: editingPackageId.value }),
        {
            preserveScroll: true,
            onSuccess: cancelEditPackage,
        },
    );
};

const descriptionCreateForms = reactive<Record<number, any>>({});
const getDescriptionCreateForm = (packageId: number) => {
    if (!descriptionCreateForms[packageId]) {
        descriptionCreateForms[packageId] = useForm({ title: '' });
    }
    return descriptionCreateForms[packageId];
};

const createDescription = (packageId: number) => {
    const f = getDescriptionCreateForm(packageId);
    f.post(route('service.packages.descriptions.store', { service: props.service.id, package: packageId }), {
        preserveScroll: true,
        onSuccess: () => f.reset(),
    });
};

const editingDescription = ref<{ packageId: number; descriptionId: number } | null>(null);
const descriptionEditForm = useForm({ title: '' });

const startEditDescription = (packageId: number, desc: PackageDescription) => {
    editingDescription.value = { packageId, descriptionId: desc.id };
    descriptionEditForm.setDefaults({ title: desc.title });
    descriptionEditForm.reset();
};

const cancelEditDescription = () => {
    editingDescription.value = null;
    descriptionEditForm.reset();
    descriptionEditForm.clearErrors();
};

const updateDescription = () => {
    if (!editingDescription.value) return;
    descriptionEditForm.put(
        route('service.packages.descriptions.update', {
            service: props.service.id,
            package: editingDescription.value.packageId,
            description: editingDescription.value.descriptionId,
        }),
        {
            preserveScroll: true,
            onSuccess: cancelEditDescription,
        },
    );
};
</script>

<template>
    <Head title="Edit Service" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-muted/40 py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <HeadingSmall title="Edit Service" description="Ubah data service, kategori, paket, dan deskripsinya." />
                    <Button as-child variant="outline" class="rounded-xl">
                        <Link :href="route('service.index')">Kembali</Link>
                    </Button>
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <h2 class="mb-4 text-base font-semibold">Data Service</h2>
                    <ServiceForm v-model="serviceForm" :category-options="categoryOptions" submit-label="Update" @submit="submitService" />
                </div>

                <div class="rounded-2xl border bg-background p-6 shadow-sm">
                    <h2 class="mb-4 text-base font-semibold">Paket Service</h2>

                <form class="grid gap-3 md:grid-cols-4" @submit.prevent="createPackage">
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="package_title">Judul Paket</Label>
                        <Input id="package_title" v-model="packageCreateForm.title" placeholder="Judul paket" class="rounded-xl" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="package_price">Harga</Label>
                        <Input id="package_price" v-model="packageCreateForm.price" placeholder="0" class="rounded-xl" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="package_type">Type</Label>
                        <Input id="package_type" v-model="packageCreateForm.type" placeholder="(opsional)" class="rounded-xl" />
                    </div>
                    <div class="grid gap-2 md:col-span-4">
                        <Label for="package_days">Days</Label>
                        <Input id="package_days" v-model="packageCreateForm.days" type="number" min="1" class="rounded-xl" />
                    </div>

                    <div class="md:col-span-4">
                        <Button type="submit" :disabled="packageCreateForm.processing">Tambah Paket</Button>
                    </div>
                </form>

                <div class="mt-6 space-y-4">
                    <div v-for="pkg in packages" :key="pkg.id" class="rounded-2xl border border-sidebar-border/70 p-4">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div class="flex-1">
                                <template v-if="editingPackageId === pkg.id">
                                    <div class="grid gap-3 md:grid-cols-3">
                                        <div class="grid gap-2 md:col-span-2">
                                            <Label>Judul</Label>
                                            <Input v-model="packageEditForm.title" class="rounded-xl" />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label>Harga</Label>
                                            <Input v-model="packageEditForm.price" class="rounded-xl" />
                                        </div>
                                        <div class="grid gap-2 md:col-span-3">
                                            <Label>Type</Label>
                                            <Input v-model="packageEditForm.type" class="rounded-xl" />
                                        </div>
                                        <div class="grid gap-2 md:col-span-3">
                                            <Label>Days</Label>
                                            <Input v-model="packageEditForm.days" type="number" min="1" class="rounded-xl" />
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="font-semibold">{{ pkg.title }}</div>
                                    <div class="text-sm text-muted-foreground">
                                        Harga: {{ pkg.price }} | Type: {{ pkg.type ?? '-' }} | Days: {{ pkg.days ?? 7 }}
                                    </div>
                                </template>
                            </div>

                            <div class="flex items-center gap-2">
                                <template v-if="editingPackageId === pkg.id">
                                    <Button type="button" size="sm" @click="updatePackage" :disabled="packageEditForm.processing">
                                        Simpan
                                    </Button>
                                    <Button type="button" variant="outline" size="sm" @click="cancelEditPackage">Batal</Button>
                                </template>
                                <template v-else>
                                    <Button type="button" variant="outline" size="sm" @click="startEditPackage(pkg)">Edit</Button>
                                    <Button as-child variant="destructive" size="sm">
                                        <Link
                                            :href="route('service.packages.destroy', { service: service.id, package: pkg.id })"
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

                        <div class="mt-4">
                            <div class="mb-2 font-medium">Deskripsi Paket</div>

                            <div class="space-y-2">
                                <div v-if="pkg.descriptions.length === 0" class="text-sm text-muted-foreground">Belum ada deskripsi</div>

                                <div
                                    v-for="desc in pkg.descriptions"
                                    :key="desc.id"
                                    class="flex flex-col gap-2 rounded-xl border border-sidebar-border/70 p-3 md:flex-row md:items-center md:justify-between"
                                >
                                    <template v-if="editingDescription?.descriptionId === desc.id">
                                        <div class="flex-1">
                                            <Input v-model="descriptionEditForm.title" class="rounded-xl" />
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <Button type="button" size="sm" @click="updateDescription" :disabled="descriptionEditForm.processing">
                                                Simpan
                                            </Button>
                                            <Button type="button" variant="outline" size="sm" @click="cancelEditDescription">Batal</Button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="flex-1 text-sm">{{ desc.title }}</div>
                                        <div class="flex items-center gap-2">
                                            <Button type="button" variant="outline" size="sm" @click="startEditDescription(pkg.id, desc)">
                                                Edit
                                            </Button>
                                            <Button as-child variant="destructive" size="sm">
                                                <Link
                                                    :href="
                                                        route('service.packages.descriptions.destroy', {
                                                            service: service.id,
                                                            package: pkg.id,
                                                            description: desc.id,
                                                        })
                                                    "
                                                    method="delete"
                                                    as="button"
                                                    preserve-scroll
                                                >
                                                    Hapus
                                                </Link>
                                            </Button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <form class="mt-3 flex flex-col gap-2 md:flex-row" @submit.prevent="createDescription(pkg.id)">
                                <div class="flex-1">
                                    <Input v-model="getDescriptionCreateForm(pkg.id).title" placeholder="Tambah deskripsi..." class="rounded-xl" />
                                </div>
                                <Button type="submit" :disabled="getDescriptionCreateForm(pkg.id).processing">Tambah</Button>
                            </form>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
