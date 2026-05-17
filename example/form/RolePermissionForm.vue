<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import SearchableInputSelect, { type SearchableOption } from '@/components/custom/SearchableInputSelect.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';

defineProps<{
    permissionOptions: SearchableOption[];
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
            <Label>Permissions</Label>
            <SearchableInputSelect v-model="form.permission_names" :options="permissionOptions" multiple placeholder="Pilih permission" />
            <InputError :message="form.errors.permission_names" />
        </div>

        <div class="flex items-center gap-3">
            <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
        </div>
    </form>
</template>
