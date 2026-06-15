<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center overflow-hidden bg-slate-50 p-4 sm:p-6 md:p-10 dark:bg-slate-950"
    >
        <div
            class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(59,130,246,0.14),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(99,102,241,0.12),transparent_34%)]"
        />

        <div class="relative flex w-full max-w-md flex-col gap-5">
            <Link
                :href="home()"
                class="flex items-center gap-3 self-center font-semibold"
            >
                <div
                    class="flex size-12 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-lg shadow-primary/20"
                >
                    <AppLogoIcon class="size-7 fill-current" />
                </div>
            </Link>

            <Card
                class="gap-0 rounded-2xl border-white/80 bg-card/95 py-0 shadow-2xl shadow-slate-900/10 backdrop-blur dark:border-white/10 dark:shadow-black/30"
            >
                <CardHeader
                    class="border-b border-border/70 px-6 py-7 text-center sm:px-10 sm:py-8"
                >
                    <CardTitle class="text-2xl tracking-tight">
                        {{ title }}
                    </CardTitle>
                    <CardDescription class="mt-1">
                        {{ description }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="px-6 py-7 sm:px-10 sm:py-8">
                    <slot />
                </CardContent>
            </Card>
        </div>
    </div>
</template>
