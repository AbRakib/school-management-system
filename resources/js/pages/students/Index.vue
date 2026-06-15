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

type Student = {
    id: number;
    admission_number: string;
    name: string;
    gender: string | null;
    admission_date: string;
    school_class: { id: number; name: string };
    guardian: { id: number; name: string; phone: string };
    image_url: string;
};

defineProps<{ students: Student[] }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Students', href: '/students' }],
    },
});

const remove = (student: Student) => {
    if (confirm(`Delete ${student.name}?`)) {
        router.delete(`/students/${student.id}`);
    }
};
</script>

<template>
    <Head title="Students" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Students"
            description="Manage admissions, class placement, and parent links."
            action-label="Admit student"
            action-href="/students/create"
        />

        <Card>
            <CardHeader><CardTitle>Student directory</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-200 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">SL</th>
                            <th class="px-3 py-3 font-medium">Admission no.</th>
                            <th class="px-3 py-3 font-medium">Student</th>
                            <th class="px-3 py-3 font-medium">Class</th>
                            <th class="px-3 py-3 font-medium">Parent</th>
                            <th class="px-3 py-3 font-medium">
                                Admission date
                            </th>
                            <th class="px-3 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(student, index) in students"
                            :key="student.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-3 py-4 text-muted-foreground">
                                {{ index + 1 }}
                            </td>
                            <td class="px-3 py-4">
                                <Badge variant="secondary">
                                    {{ student.admission_number }}
                                </Badge>
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex items-center gap-3">
                                    <Avatar class="size-10 border">
                                        <AvatarImage
                                            :src="student.image_url"
                                            :alt="student.name"
                                            class="object-cover"
                                        />
                                    </Avatar>
                                    <div>
                                        <div class="font-medium">
                                            {{ student.name }}
                                        </div>
                                        <div
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ student.gender }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                {{ student.school_class.name }}
                            </td>
                            <td class="px-3 py-4">
                                <div>{{ student.guardian.name }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ student.guardian.phone }}
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                {{ student.admission_date }}
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex justify-end">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                aria-label="Open student actions"
                                            >
                                                <Ellipsis class="size-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="`/students/${student.id}/edit`"
                                                >
                                                    <Pencil />
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                variant="destructive"
                                                @select="remove(student)"
                                            >
                                                <Trash2 />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="students.length === 0">
                            <td
                                colspan="7"
                                class="px-3 py-12 text-center text-muted-foreground"
                            >
                                No students have been admitted yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
