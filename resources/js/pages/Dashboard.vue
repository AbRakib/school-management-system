<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { GraduationCap, School, Users, UsersRound } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

type Stats = {
    students: number;
    teachers: number;
    parents: number;
    classes: number;
};

type SchoolClass = {
    id: number;
    name: string;
    grade_level: number;
    section: string | null;
    students_count: number;
    class_teacher: { id: number; name: string } | null;
};

type RecentStudent = {
    id: number;
    admission_number: string;
    name: string;
    admission_date: string;
    school_class: { id: number; name: string };
    guardian: { id: number; name: string };
};

const props = defineProps<{
    stats: Stats;
    classes: SchoolClass[];
    recentStudents: RecentStudent[];
}>();

const statCards = [
    {
        label: 'Students',
        value: props.stats.students,
        href: '/students',
        icon: GraduationCap,
        color: 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
    },
    {
        label: 'Teachers',
        value: props.stats.teachers,
        href: '/teachers',
        icon: Users,
        color: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    },
    {
        label: 'Parents',
        value: props.stats.parents,
        href: '/guardians',
        icon: UsersRound,
        color: 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
    },
    {
        label: 'Classes',
        value: props.stats.classes,
        href: '/classes',
        icon: School,
        color: 'bg-violet-500/10 text-violet-600 dark:text-violet-400',
    },
];

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="space-y-6 p-4 md:p-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">
                School dashboard
            </h1>
            <p class="mt-1 text-sm text-muted-foreground">
                An overview of students, staff, parents, and classrooms.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Link
                v-for="item in statCards"
                :key="item.label"
                :href="item.href"
                class="rounded-xl focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
            >
                <Card
                    class="h-full transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <CardContent class="flex items-center gap-4">
                        <div
                            :class="[
                                'flex size-12 items-center justify-center rounded-xl',
                                item.color,
                            ]"
                        >
                            <component :is="item.icon" class="size-6" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">
                                {{ item.label }}
                            </p>
                            <p class="text-3xl font-semibold">
                                {{ item.value }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <div class="grid gap-6 xl:grid-cols-5">
            <Card class="xl:col-span-3">
                <CardHeader
                    class="flex-row items-center justify-between space-y-0"
                >
                    <CardTitle>Class overview</CardTitle>
                    <Button variant="outline" size="sm" as-child>
                        <Link href="/classes">Manage classes</Link>
                    </Button>
                </CardHeader>
                <CardContent class="overflow-x-auto">
                    <table class="w-full min-w-130 text-sm">
                        <thead class="border-b text-left text-muted-foreground">
                            <tr>
                                <th class="py-3 font-medium">Class</th>
                                <th class="py-3 font-medium">Teacher</th>
                                <th class="py-3 text-right font-medium">
                                    Students
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="schoolClass in classes"
                                :key="schoolClass.id"
                                class="border-b last:border-0"
                            >
                                <td class="py-3 font-medium">
                                    {{ schoolClass.name }}
                                    <span
                                        v-if="schoolClass.section"
                                        class="text-muted-foreground"
                                    >
                                        ({{ schoolClass.section }})
                                    </span>
                                </td>
                                <td class="py-3 text-muted-foreground">
                                    {{
                                        schoolClass.class_teacher?.name ||
                                        'Not assigned'
                                    }}
                                </td>
                                <td class="py-3 text-right">
                                    <Badge variant="secondary">
                                        {{ schoolClass.students_count }}
                                    </Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <Card class="xl:col-span-2">
                <CardHeader
                    class="flex-row items-center justify-between space-y-0"
                >
                    <CardTitle>Recent admissions</CardTitle>
                    <Button variant="outline" size="sm" as-child>
                        <Link href="/students">View all</Link>
                    </Button>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        v-for="student in recentStudents"
                        :key="student.id"
                        class="flex items-center justify-between gap-4 border-b pb-4 last:border-0 last:pb-0"
                    >
                        <div class="min-w-0">
                            <p class="truncate font-medium">
                                {{ student.name }}
                            </p>
                            <p class="truncate text-xs text-muted-foreground">
                                {{ student.school_class.name }} ·
                                {{ student.guardian.name }}
                            </p>
                        </div>
                        <Badge variant="outline">
                            {{ student.admission_number }}
                        </Badge>
                    </div>
                    <p
                        v-if="recentStudents.length === 0"
                        class="py-8 text-center text-sm text-muted-foreground"
                    >
                        No students admitted yet.
                    </p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
