<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { Button } from '@/components/ui/button';

defineProps<{
    title: string;
    description: string;
    actionLabel?: string;
    actionHref?: string;
}>();
</script>

<template>
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">{{ title }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                {{ description }}
            </p>
        </div>

        <Button v-if="actionHref && actionLabel" as-child>
            <Link :href="actionHref">
                <Plus class="size-4" />
                {{ actionLabel }}
            </Link>
        </Button>
    </div>
</template>
