<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Ellipsis, Pencil, Trash2 } from '@lucide/vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type Guardian = {
    id: number;
    name: string;
    relationship: string;
    phone: string;
    email: string | null;
    occupation: string | null;
    students_count: number;
    image_url: string;
};

defineProps<{ guardians: Guardian[] }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Parents', href: '/guardians' }],
    },
});

const remove = (guardian: Guardian) => {
    if (confirm(`Delete ${guardian.name}?`)) {
        router.delete(`/guardians/${guardian.id}`);
    }
};
</script>

<template>
    <Head title="Parents" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Parents"
            description="Manage parents and guardians linked to students."
            action-label="Add parent"
            action-href="/guardians/create"
        />

        <Card>
            <CardHeader><CardTitle>Parent directory</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">SL</th>
                            <th class="px-3 py-3 font-medium">Name</th>
                            <th class="px-3 py-3 font-medium">Relationship</th>
                            <th class="px-3 py-3 font-medium">Contact</th>
                            <th class="px-3 py-3 font-medium">Occupation</th>
                            <th class="px-3 py-3 font-medium">Children</th>
                            <th class="px-3 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(guardian, index) in guardians"
                            :key="guardian.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-3 py-4 text-muted-foreground">
                                {{ index + 1 }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex items-center gap-3">
                                    <Avatar class="size-10 border">
                                        <AvatarImage
                                            :src="guardian.image_url"
                                            :alt="guardian.name"
                                            class="object-cover"
                                        />
                                    </Avatar>
                                    <span class="font-medium">
                                        {{ guardian.name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                {{ guardian.relationship }}
                            </td>
                            <td class="px-3 py-4">
                                <div>{{ guardian.phone }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ guardian.email }}
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                {{ guardian.occupation || '—' }}
                            </td>
                            <td class="px-3 py-4">
                                {{ guardian.students_count }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex justify-end">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                aria-label="Open parent actions"
                                            >
                                                <Ellipsis class="size-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="`/guardians/${guardian.id}/edit`"
                                                >
                                                    <Pencil />
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                variant="destructive"
                                                @select="remove(guardian)"
                                            >
                                                <Trash2 />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="guardians.length === 0">
                            <td
                                colspan="7"
                                class="px-3 py-12 text-center text-muted-foreground"
                            >
                                No parents have been added yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
