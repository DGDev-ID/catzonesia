<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import SearchableInputSelect, { type SearchableOption } from '@/components/custom/SearchableInputSelect.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { X } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    categoryOptions: SearchableOption[];
    submitLabel: string;
    existingCoverImage?: string | null;
    existingImages?: { id: number; img_url: string }[];
}>();

const emit = defineEmits<{
    (e: 'submit'): void;
    (e: 'remove-existing-image', id: number): void;
}>();

const form = defineModel<any>({ required: true });

// ── Cover image ──
const coverPreview = ref<string | null>(null);
const showExistingCover = ref(!!(props.existingCoverImage));

function handleCoverChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0] || null;
    form.value.image = file;
    form.value.remove_image = false;
    coverPreview.value = file ? URL.createObjectURL(file) : null;
}

function removeExistingCover() {
    showExistingCover.value = false;
    form.value.image = null;
    form.value.remove_image = true;
    coverPreview.value = null;
}

// ── Gallery images ──
const galleryPreviews = ref<{ file: File; url: string }[]>([]);

function handleGalleryChange(e: Event) {
    const files = Array.from((e.target as HTMLInputElement).files ?? []);
    galleryPreviews.value = files.map((file) => ({
        file,
        url: URL.createObjectURL(file),
    }));
    form.value.images = files;
}

function removeGalleryPreview(index: number) {
    galleryPreviews.value.splice(index, 1);
    form.value.images = galleryPreviews.value.map((p) => p.file);
}
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <!-- Judul -->
        <div class="grid gap-2">
            <Label for="title">Judul</Label>
            <Input id="title" v-model="form.title" placeholder="Judul portfolio" class="rounded-xl" />
            <InputError :message="form.errors.title" />
        </div>

        <!-- Deskripsi -->
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

        <!-- Cover Image -->
        <div class="grid gap-2">
            <Label for="cover_image">
                Cover Gambar
                <span class="ml-1 text-xs text-muted-foreground font-normal">(Wajib saat membuat, opsional saat edit)</span>
            </Label>

            <!-- Existing cover image (saat edit) -->
            <div v-if="showExistingCover && existingCoverImage" class="flex items-start gap-3">
                <div class="group relative h-32 w-auto overflow-hidden rounded-lg border">
                    <img :src="`/storage/${existingCoverImage}`" class="h-full w-auto object-cover" alt="cover tersimpan" />
                    <button
                        type="button"
                        class="absolute inset-0 flex flex-col items-center justify-center gap-1 bg-black/60 opacity-0 transition-opacity group-hover:opacity-100"
                        title="Hapus gambar cover"
                        @click="removeExistingCover"
                    >
                        <X class="h-5 w-5 text-white" />
                        <span class="text-[10px] text-white font-medium">Hapus</span>
                    </button>
                </div>
                <span class="text-xs text-muted-foreground mt-1">Hover gambar untuk menghapusnya</span>
            </div>

            <!-- Preview gambar baru yang dipilih -->
            <div v-if="coverPreview" class="relative w-fit">
                <img :src="coverPreview" class="h-32 w-auto rounded-lg border object-cover" alt="preview cover baru" />
                <span class="mt-1 block text-xs text-muted-foreground">Preview gambar baru</span>
            </div>

            <!-- Input file cover: tampil jika tidak ada existing cover atau sudah dihapus -->
            <div v-if="!showExistingCover || !existingCoverImage">
                <Input
                    id="cover_image"
                    type="file"
                    accept="image/*"
                    class="cursor-pointer rounded-xl"
                    @change="handleCoverChange"
                />
            </div>
            <InputError :message="form.errors.image" />
        </div>

        <!-- Galeri Gambar (Multiple) -->
        <div class="grid gap-2">
            <Label for="gallery_images">
                Galeri Gambar
                <span class="ml-1 text-xs text-muted-foreground font-normal">(Opsional, bisa lebih dari satu)</span>
            </Label>

            <!-- Existing gallery images (saat edit) -->
            <div v-if="existingImages && existingImages.length > 0" class="flex flex-wrap gap-2">
                <div
                    v-for="img in existingImages"
                    :key="img.id"
                    class="group relative h-20 w-20 overflow-hidden rounded-lg border"
                >
                    <img :src="`/storage/${img.img_url}`" class="h-full w-full object-cover" alt="galeri" />
                    <button
                        type="button"
                        class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100"
                        title="Hapus gambar"
                        @click="emit('remove-existing-image', img.id)"
                    >
                        <X class="h-5 w-5 text-white" />
                    </button>
                </div>
            </div>

            <!-- Preview newly selected images -->
            <div v-if="galleryPreviews.length > 0" class="flex flex-wrap gap-2">
                <div
                    v-for="(preview, i) in galleryPreviews"
                    :key="i"
                    class="group relative h-20 w-20 overflow-hidden rounded-lg border"
                >
                    <img :src="preview.url" class="h-full w-full object-cover" alt="preview" />
                    <button
                        type="button"
                        class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 transition-opacity group-hover:opacity-100"
                        @click="removeGalleryPreview(i)"
                    >
                        <X class="h-5 w-5 text-white" />
                    </button>
                </div>
            </div>

            <Input
                id="gallery_images"
                type="file"
                accept="image/*"
                multiple
                class="cursor-pointer rounded-xl"
                @change="handleGalleryChange"
            />
            <InputError :message="form.errors.images" />
        </div>

        <!-- Link URL -->
        <div class="grid gap-2">
            <Label for="link_url">Link URL <span class="ml-1 text-xs text-muted-foreground font-normal">(Opsional)</span></Label>
            <Input id="link_url" v-model="form.link_url" placeholder="https://..." class="rounded-xl" />
            <InputError :message="form.errors.link_url" />
        </div>

        <!-- Kategori -->
        <div class="grid gap-2">
            <Label>Kategori</Label>
            <SearchableInputSelect v-model="form.category_ids" :options="categoryOptions" multiple placeholder="Pilih kategori" />
            <InputError :message="form.errors.category_ids" />
        </div>

        <div class="flex items-center gap-3">
            <Button type="submit" :disabled="form.processing">{{ submitLabel }}</Button>
        </div>
    </form>
</template>
