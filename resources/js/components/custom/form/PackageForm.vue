<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

interface PackageFormData {
    name: string;
    price: number;
    is_grooming: boolean;
    description: string;
}

const props = withDefaults(defineProps<{
    initialData?: Partial<PackageFormData>;
    submitLabel?: string;
    backUrl?: string;
}>(), {
    submitLabel: 'Simpan',
    backUrl: '/master/package',
});

const emit = defineEmits<{
    submit: [data: PackageFormData];
}>();

const form = ref<PackageFormData>({
    name: props.initialData?.name ?? '',
    price: props.initialData?.price ?? 0,
    is_grooming: props.initialData?.is_grooming ?? false,
    description: props.initialData?.description ?? '',
});

const handleSubmit = () => {
    emit('submit', { ...form.value });
};
</script>

<template>
    <div class="rounded-2xl border bg-background shadow-sm p-8 max-w-lg">
        <div class="grid grid-cols-1 gap-6">
            <div>
                <label for="pkg-name" class="mb-2 block text-sm font-medium">Nama Paket</label>
                <input
                    id="pkg-name"
                    v-model="form.name"
                    type="text"
                    placeholder="Masukkan nama paket"
                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                />
            </div>

            <div>
                <label for="pkg-price" class="mb-2 block text-sm font-medium">Harga</label>
                <input
                    id="pkg-price"
                    v-model="form.price"
                    type="number"
                    placeholder="Masukkan harga"
                    class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                />
            </div>

            <div>
                <label for="pkg-desc" class="mb-2 block text-sm font-medium">Deskripsi</label>
                <textarea
                    id="pkg-desc"
                    v-model="form.description"
                    rows="3"
                    placeholder="Masukkan deskripsi paket"
                    class="flex w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                ></textarea>
            </div>

            <div class="flex items-center gap-3">
                <input
                    id="pkg-grooming"
                    v-model="form.is_grooming"
                    type="checkbox"
                    class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                />
                <label for="pkg-grooming" class="text-sm font-medium">Paket Grooming</label>
            </div>
        </div>

        <div class="mt-8 flex gap-3">
            <button
                type="button"
                @click="handleSubmit"
                class="inline-flex items-center justify-center rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-primary-foreground shadow-sm transition hover:opacity-90"
            >
                {{ submitLabel }}
            </button>
            <Link
                :href="backUrl"
                class="inline-flex items-center justify-center rounded-xl border border-input bg-background px-5 py-2.5 text-sm font-medium text-foreground shadow-sm transition hover:bg-muted"
            >
                Batal
            </Link>
        </div>
    </div>
</template>
