<script setup lang="ts">
import { Separator } from '@/components/ui/separator';

interface Props {
    title: string;
    description?: string;
    variant?: 'default' | 'small';
}

defineProps<Props>();
</script>

<template>
    <div class="space-y-0.5" :class="variant === 'small' ? '' : 'mb-8'">
        <h2 class="text-xl font-semibold tracking-tight">{{ title }}</h2>
        <p v-if="description" class="text-sm text-muted-foreground">
            {{ description }}
        </p>
    </div>
    <Separator v-if="!variant || variant === 'default'" class="my-6" />
</template>
