<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import SearchableInputSelect, { type SearchableOption } from '@/components/custom/SearchableInputSelect.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineProps<{
    categoryOptions: SearchableOption[];
    submitLabel: string;
}>();

const form = defineModel<any>({ required: true });

const emits = defineEmits<{
    (e: 'submit'): void;
}>();
</script>

<template>
    <form @submit.prevent="emits('submit')" class="space-y-6">
        <div class="grid gap-2">
            <Label for="title">Judul</Label>
            <Input id="title" v-model="form.title" placeholder="Judul service" class="rounded-xl" />
            <InputError :message="form.errors.title" />
        </div>

        <div class="grid gap-2">
            <Label for="description">Deskripsi</Label>
            <textarea
                id="description"
                v-model="form.description"
                class="flex min-h-[96px] w-full rounded-xl border border-input bg-background px-3 py-2 text-base ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                placeholder="Deskripsi"
            />
            <InputError :message="form.errors.description" />
        </div>

        <div class="grid gap-2">
            <Label>Kategori</Label>
            <SearchableInputSelect v-model="form.category_ids" :options="categoryOptions" multiple placeholder="Pilih kategori" />
            <InputError :message="form.errors.category_ids" />
        </div>

        <div class="flex items-center justify-between gap-4 rounded-2xl border border-sidebar-border/70 bg-muted/20 px-4 py-3">
            <div>
                <div class="text-sm font-medium">Timeline</div>
                <div class="text-xs text-muted-foreground">Tampilkan service ini di halaman timeline.</div>
            </div>
            <Button
                type="button"
                :variant="form.is_timeline ? 'default' : 'outline'"
                class="rounded-xl"
                @click="form.is_timeline = !form.is_timeline"
            >
                {{ form.is_timeline ? 'Aktif' : 'Nonaktif' }}
            </Button>
        </div>
        <InputError :message="form.errors.is_timeline" />

        <div class="flex items-center gap-3">
            <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
        </div>
    </form>
</template>
