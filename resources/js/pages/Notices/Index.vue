<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

type Notice = {
    id: number;
    title: string;
    description: string;
    audience: string;
    publish_date: string;
    expiry_date: string | null;
    attachment: string | null;
    class_room: { id: number; name: string } | null;
    created_by: { id: number; name: string } | null;
};

defineProps<{
    notices: {
        data: Notice[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    canManage: boolean;
}>();

const openId = ref<number | null>(null);
const attachmentUrl = (path: string) => `/storage/${path}`;

defineOptions({
    layout: { breadcrumbs: [{ title: 'Notices', href: '/notices' }] },
});
</script>

<template>
    <Head title="Notices" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Notice Board"
            description="Current school notices and announcements."
        />

        <div v-if="canManage" class="flex justify-end">
            <Button as-child>
                <Link href="/notices/create">Add Notice</Link>
            </Button>
        </div>

        <div class="space-y-4">
            <Card v-for="notice in notices.data" :key="notice.id">
                <CardContent class="space-y-4 pt-6">
                    <div
                        class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between"
                    >
                        <button
                            class="min-w-0 text-left"
                            @click="
                                openId = openId === notice.id ? null : notice.id
                            "
                        >
                            <h2 class="text-lg font-semibold">
                                {{ notice.title }}
                            </h2>
                            <div
                                class="mt-2 flex flex-wrap gap-2 text-xs text-muted-foreground"
                            >
                                <Badge variant="secondary">{{
                                    notice.audience.replace('_', ' ')
                                }}</Badge>
                                <Badge
                                    v-if="notice.class_room"
                                    variant="outline"
                                >
                                    {{ notice.class_room.name }}
                                </Badge>
                                <span>Published {{ notice.publish_date }}</span>
                                <span v-if="notice.expiry_date"
                                    >Expires {{ notice.expiry_date }}</span
                                >
                            </div>
                        </button>
                        <div v-if="canManage" class="flex gap-2">
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/notices/${notice.id}/edit`"
                                    >Edit</Link
                                >
                            </Button>
                            <Button
                                variant="ghost"
                                size="sm"
                                @click="router.delete(`/notices/${notice.id}`)"
                            >
                                Delete
                            </Button>
                        </div>
                    </div>

                    <div
                        v-if="openId === notice.id"
                        class="space-y-3 border-t pt-4"
                    >
                        <p class="text-sm leading-6 whitespace-pre-line">
                            {{ notice.description }}
                        </p>
                        <Button
                            v-if="notice.attachment"
                            variant="outline"
                            size="sm"
                            as-child
                        >
                            <a :href="attachmentUrl(notice.attachment)" download
                                >Download attachment</a
                            >
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="flex flex-wrap gap-2">
            <Button
                v-for="link in notices.links"
                :key="link.label"
                variant="outline"
                size="sm"
                :disabled="!link.url"
                :class="{ 'bg-muted': link.active }"
                @click="link.url && router.get(link.url)"
                v-html="link.label"
            />
        </div>
    </div>
</template>
