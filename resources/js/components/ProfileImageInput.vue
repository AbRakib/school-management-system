<script setup lang="ts">
import { onUnmounted, ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = withDefaults(
    defineProps<{
        modelValue: File | null;
        currentImageUrl?: string | null;
        defaultImageUrl: string;
        label?: string;
        attributionUrl: string;
        attributionLabel: string;
    }>(),
    {
        currentImageUrl: null,
        label: 'Profile image',
    },
);

const emit = defineEmits<{
    'update:modelValue': [file: File | null];
}>();

const previewUrl = ref(props.currentImageUrl || props.defaultImageUrl);
let objectUrl: string | null = null;

const revokeObjectUrl = () => {
    if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        objectUrl = null;
    }
};

watch(
    () => props.modelValue,
    (file) => {
        revokeObjectUrl();

        if (file) {
            objectUrl = URL.createObjectURL(file);
            previewUrl.value = objectUrl;
        } else {
            previewUrl.value = props.currentImageUrl || props.defaultImageUrl;
        }
    },
);

watch(
    () => props.currentImageUrl,
    (url) => {
        if (!props.modelValue) {
            previewUrl.value = url || props.defaultImageUrl;
        }
    },
);

const selectImage = (event: Event) => {
    const input = event.target as HTMLInputElement;
    emit('update:modelValue', input.files?.[0] ?? null);
};

onUnmounted(revokeObjectUrl);
</script>

<template>
    <div class="flex items-center gap-4">
        <img
            :src="previewUrl"
            alt="Profile image preview"
            class="size-20 rounded-full border object-cover"
        />
        <div class="grid flex-1 gap-2">
            <Label for="profile_image">{{ label }} (optional)</Label>
            <Input
                id="profile_image"
                type="file"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                @change="selectImage"
            />
            <p class="text-xs text-muted-foreground">
                JPG, PNG, or WebP. Maximum size 2 MB.
            </p>
            <a
                :href="attributionUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="w-fit text-xs text-muted-foreground underline underline-offset-4 hover:text-foreground"
            >
                {{ attributionLabel }}
            </a>
        </div>
    </div>
</template>
