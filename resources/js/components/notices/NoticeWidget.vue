<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Megaphone } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Notice = {
    id: number;
    title: string;
    audience: string;
    publish_date: string;
    expiry_date: string | null;
};

defineProps<{ notices: Notice[] }>();
</script>

<template>
    <Card>
        <CardHeader class="flex-row items-center justify-between space-y-0">
            <CardTitle>Recent notices</CardTitle>
            <Button variant="outline" size="sm" as-child>
                <Link href="/notices">View all</Link>
            </Button>
        </CardHeader>
        <CardContent class="space-y-3">
            <Link
                v-for="notice in notices"
                :key="notice.id"
                href="/notices"
                class="flex items-start gap-3 rounded-lg border p-3 text-sm transition hover:bg-muted/50"
            >
                <Megaphone class="mt-0.5 size-4 text-primary" />
                <span class="min-w-0 flex-1">
                    <span class="block truncate font-medium">{{
                        notice.title
                    }}</span>
                    <span
                        class="mt-1 flex flex-wrap gap-2 text-xs text-muted-foreground"
                    >
                        <Badge variant="secondary">{{
                            notice.audience.replace('_', ' ')
                        }}</Badge>
                        <span>{{ notice.publish_date }}</span>
                    </span>
                </span>
            </Link>
            <p
                v-if="notices.length === 0"
                class="text-sm text-muted-foreground"
            >
                No active notices right now.
            </p>
        </CardContent>
    </Card>
</template>
