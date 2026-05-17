<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

defineProps<{
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
            <Input id="title" v-model="form.title" placeholder="Judul kategori" class="rounded-xl" />
            <InputError :message="form.errors.title" />
        </div>

        <div class="flex items-center gap-3">
            <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
        </div>
    </form>
</template>
