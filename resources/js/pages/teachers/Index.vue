<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Ellipsis, Pencil, Trash2 } from '@lucide/vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

type Teacher = {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    designation: 'teacher' | 'head_teacher';
    subject: string | null;
    classes_count: number;
    image_url: string;
};

defineProps<{ teachers: Teacher[] }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Teachers', href: '/teachers' }],
    },
});

const remove = (teacher: Teacher) => {
    if (confirm(`Delete ${teacher.name}?`)) {
        router.delete(`/teachers/${teacher.id}`);
    }
};
</script>

<template>
    <Head title="Teachers" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Teachers"
            description="Manage teachers and the head teacher."
            action-label="Add teacher"
            action-href="/teachers/create"
        />

        <Card>
            <CardHeader>
                <CardTitle>Teacher directory</CardTitle>
            </CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">SL</th>
                            <th class="px-3 py-3 font-medium">Name</th>
                            <th class="px-3 py-3 font-medium">Role</th>
                            <th class="px-3 py-3 font-medium">Subject</th>
                            <th class="px-3 py-3 font-medium">Contact</th>
                            <th class="px-3 py-3 font-medium">Classes</th>
                            <th class="px-3 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(teacher, index) in teachers"
                            :key="teacher.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-3 py-4 text-muted-foreground">
                                {{ index + 1 }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex items-center gap-3">
                                    <Avatar class="size-10 border">
                                        <AvatarImage
                                            :src="teacher.image_url"
                                            :alt="teacher.name"
                                            class="object-cover"
                                        />
                                    </Avatar>
                                    <span class="font-medium">
                                        {{ teacher.name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <Badge
                                    :variant="
                                        teacher.designation === 'head_teacher'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        teacher.designation === 'head_teacher'
                                            ? 'Head Teacher'
                                            : 'Teacher'
                                    }}
                                </Badge>
                            </td>
                            <td class="px-3 py-4">
                                {{ teacher.subject || '—' }}
                            </td>
                            <td class="px-3 py-4">
                                <div>{{ teacher.phone || '—' }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ teacher.email }}
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                {{ teacher.classes_count }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex justify-end">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                aria-label="Open teacher actions"
                                            >
                                                <Ellipsis class="size-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="`/teachers/${teacher.id}/edit`"
                                                >
                                                    <Pencil />
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                variant="destructive"
                                                @select="remove(teacher)"
                                            >
                                                <Trash2 />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="teachers.length === 0">
                            <td
                                colspan="7"
                                class="px-3 py-12 text-center text-muted-foreground"
                            >
                                No teachers have been added yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
