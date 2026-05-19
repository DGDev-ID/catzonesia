<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = withDefaults(defineProps<{
    initialName?: string;
    submitLabel?: string;
    backUrl?: string;
}>(), {
    initialName: '',
    submitLabel: 'Simpan',
    backUrl: '/master/unit',
});

const emit = defineEmits<{
    submit: [data: { name: string }];
}>();

const name = ref(props.initialName);

const handleSubmit = () => {
    emit('submit', { name: name.value });
};
</script>

<template>
    <div class="rounded-2xl border bg-background shadow-sm p-8">
        <div class="mb-6">
            <label for="unit-name" class="mb-2 block text-sm font-medium">Nama Unit</label>
            <input
                id="unit-name"
                v-model="name"
                type="text"
                placeholder="Masukkan nama unit"
                class="flex h-10 w-full rounded-xl border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
            />
        </div>
        <div class="flex gap-3 justify-end">
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
