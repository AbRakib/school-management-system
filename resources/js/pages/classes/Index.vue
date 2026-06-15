<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Ellipsis, Pencil, Trash2 } from '@lucide/vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type SchoolClass = {
    id: number;
    grade_level: number;
    name: string;
    section: string | null;
    room: string | null;
    students_count: number;
    class_teacher: { id: number; name: string } | null;
};

defineProps<{ classes: SchoolClass[] }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Classes', href: '/classes' }],
    },
});

const remove = (schoolClass: SchoolClass) => {
    if (confirm(`Delete ${schoolClass.name}?`)) {
        router.delete(`/classes/${schoolClass.id}`);
    }
};
</script>

<template>
    <Head title="Classes" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Classes"
            description="Manage classrooms from Class 1 through Class 10."
            action-label="Add class"
            action-href="/classes/create"
        />

        <Card>
            <CardHeader><CardTitle>Class list</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-160 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">SL</th>
                            <th class="px-3 py-3 font-medium">Class</th>
                            <th class="px-3 py-3 font-medium">Section</th>
                            <th class="px-3 py-3 font-medium">Room</th>
                            <th class="px-3 py-3 font-medium">Class teacher</th>
                            <th class="px-3 py-3 font-medium">Students</th>
                            <th class="px-3 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(schoolClass, index) in classes"
                            :key="schoolClass.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-3 py-4 text-muted-foreground">
                                {{ index + 1 }}
                            </td>
                            <td class="px-3 py-4 font-medium">
                                {{ schoolClass.name }}
                            </td>
                            <td class="px-3 py-4">
                                {{ schoolClass.section || '—' }}
                            </td>
                            <td class="px-3 py-4">
                                {{ schoolClass.room || '—' }}
                            </td>
                            <td class="px-3 py-4">
                                {{ schoolClass.class_teacher?.name || '—' }}
                            </td>
                            <td class="px-3 py-4">
                                {{ schoolClass.students_count }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex justify-end">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                aria-label="Open class actions"
                                            >
                                                <Ellipsis class="size-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="`/classes/${schoolClass.id}/edit`"
                                                >
                                                    <Pencil />
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                variant="destructive"
                                                @select="remove(schoolClass)"
                                            >
                                                <Trash2 />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
