<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

export type SearchableOption = {
    label: string;
    value: string | number;
};

const props = withDefaults(
    defineProps<{
        options: SearchableOption[];
        modelValue: string | number | null | Array<string | number>;
        multiple?: boolean;
        placeholder?: string;
        searchPlaceholder?: string;
        disabled?: boolean;
    }>(),
    {
        multiple: false,
        placeholder: 'Pilih...',
        searchPlaceholder: 'Cari...',
        disabled: false,
    },
);

const emits = defineEmits<{
    (e: 'update:modelValue', value: string | number | null | Array<string | number>): void;
}>();

const rootRef = ref<HTMLElement | null>(null);
const isOpen = ref(false);
const query = ref('');

const normalizedValue = computed<Array<string | number>>(() => {
    if (props.multiple) return Array.isArray(props.modelValue) ? props.modelValue : [];
    return props.modelValue === null || props.modelValue === undefined ? [] : [props.modelValue];
});

const selectedOptions = computed(() => {
    const selectedSet = new Set(normalizedValue.value);
    return props.options.filter((o) => selectedSet.has(o.value));
});

const filteredOptions = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return props.options;
    return props.options.filter((o) => o.label.toLowerCase().includes(q));
});

const displayText = computed(() => {
    if (props.multiple) {
        if (selectedOptions.value.length === 0) return '';
        return `${selectedOptions.value.length} dipilih`;
    }
    return selectedOptions.value[0]?.label ?? '';
});

const toggleOpen = () => {
    if (props.disabled) return;
    isOpen.value = !isOpen.value;
};

const close = () => {
    isOpen.value = false;
    query.value = '';
};

const isSelected = (value: string | number) => normalizedValue.value.includes(value);

const selectValue = (value: string | number) => {
    if (props.disabled) return;

    if (props.multiple) {
        const current = new Set(normalizedValue.value);
        if (current.has(value)) current.delete(value);
        else current.add(value);
        emits('update:modelValue', Array.from(current));
        return;
    }

    emits('update:modelValue', value);
    close();
};

const removeValue = (value: string | number) => {
    if (props.disabled) return;

    if (!props.multiple) {
        emits('update:modelValue', null);
        return;
    }

    const next = normalizedValue.value.filter((v) => v !== value);
    emits('update:modelValue', next);
};

const onDocumentClick = (event: MouseEvent) => {
    if (!isOpen.value) return;
    const target = event.target as Node | null;
    if (!target) return;
    if (!rootRef.value) return;
    if (!rootRef.value.contains(target)) close();
};

onMounted(() => {
    document.addEventListener('click', onDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', onDocumentClick);
});

watch(
    () => props.disabled,
    (disabled) => {
        if (disabled) close();
    },
);
</script>

<template>
    <div ref="rootRef" class="relative w-full">
        <button
            type="button"
            class="flex h-10 w-full items-center justify-between rounded-xl border border-input bg-background px-3 py-2 text-sm text-foreground ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="disabled"
            @click="toggleOpen"
        >
            <span class="truncate" :class="displayText ? 'text-foreground' : 'text-muted-foreground'">
                {{ displayText || placeholder }}
            </span>
            <span class="ml-2 text-muted-foreground">▾</span>
        </button>

        <div v-if="isOpen" class="absolute z-50 mt-2 w-full rounded-xl border border-sidebar-border/70 bg-background shadow-sm">
            <div class="p-2">
                <Input v-model="query" :placeholder="searchPlaceholder" class="rounded-xl" />
            </div>

            <div class="max-h-60 overflow-auto p-2 pt-0">
                <div v-if="filteredOptions.length === 0" class="px-2 py-3 text-sm text-muted-foreground">Tidak ada data</div>

                <div v-for="option in filteredOptions" :key="option.value" class="flex items-center justify-between gap-2">
                    <Button type="button" variant="ghost" class="h-9 w-full justify-start px-2" @click="selectValue(option.value)">
                        <span class="truncate">{{ option.label }}</span>
                    </Button>
                    <span v-if="isSelected(option.value)" class="pr-2 text-sm text-muted-foreground">✓</span>
                </div>
            </div>
        </div>

        <div v-if="multiple && selectedOptions.length > 0" class="mt-2 flex flex-wrap gap-2">
            <div
                v-for="opt in selectedOptions"
                :key="opt.value"
                class="flex items-center gap-2 rounded-full border border-sidebar-border/70 bg-muted/30 px-3 py-1 text-sm"
            >
                <span class="max-w-[220px] truncate">{{ opt.label }}</span>
                <button type="button" class="text-muted-foreground hover:text-foreground" @click="removeValue(opt.value)">×</button>
            </div>
        </div>
    </div>
</template>
