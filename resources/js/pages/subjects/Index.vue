<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Ellipsis, Pencil, Trash2 } from '@lucide/vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type Subject = {
    id: number;
    name: string;
    code: string;
    description: string | null;
    school_class: { id: number; name: string; grade_level: number } | null;
    teacher: { id: number; name: string } | null;
};

defineProps<{ subjects: Subject[] }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Subjects', href: '/subjects' }],
    },
});

const remove = (subject: Subject) => {
    if (confirm(`Delete ${subject.name}?`)) {
        router.delete(`/subjects/${subject.id}`);
    }
};
</script>

<template>
    <Head title="Subjects" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Subjects"
            description="Manage school subjects, subject codes, class levels, and assigned teachers."
            action-label="Add subject"
            action-href="/subjects/create"
        />

        <Card>
            <CardHeader>
                <CardTitle>Subject list</CardTitle>
            </CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">SL</th>
                            <th class="px-3 py-3 font-medium">Subject</th>
                            <th class="px-3 py-3 font-medium">Code</th>
                            <th class="px-3 py-3 font-medium">Class</th>
                            <th class="px-3 py-3 font-medium">Teacher</th>
                            <th class="px-3 py-3 font-medium">Description</th>
                            <th class="px-3 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(subject, index) in subjects"
                            :key="subject.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-3 py-4 text-muted-foreground">
                                {{ index + 1 }}
                            </td>
                            <td class="px-3 py-4 font-medium">
                                {{ subject.name }}
                            </td>
                            <td class="px-3 py-4">
                                <Badge variant="secondary">
                                    {{ subject.code }}
                                </Badge>
                            </td>
                            <td class="px-3 py-4">
                                {{ subject.school_class?.name || '—' }}
                            </td>
                            <td class="px-3 py-4">
                                {{ subject.teacher?.name || '—' }}
                            </td>
                            <td
                                class="max-w-70 truncate px-3 py-4 text-muted-foreground"
                            >
                                {{ subject.description || '—' }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex justify-end">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                aria-label="Open subject actions"
                                            >
                                                <Ellipsis class="size-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="`/subjects/${subject.id}/edit`"
                                                >
                                                    <Pencil />
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                variant="destructive"
                                                @select="remove(subject)"
                                            >
                                                <Trash2 />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="subjects.length === 0">
                            <td
                                colspan="7"
                                class="px-3 py-12 text-center text-muted-foreground"
                            >
                                No subjects have been added yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
