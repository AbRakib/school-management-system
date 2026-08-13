<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

type Option = { id: number; name: string };
type Exam = {
    id: number;
    title: string;
    start_date: string;
    end_date: string;
    status: string;
    exam_type: Option;
    academic_session: Option;
    class_room: Option;
};
defineProps<{
    exams: Exam[];
    filters: Record<string, string | null>;
    options: { academicSessions: Option[]; classes: Option[] };
}>();
const filter = (key: string, value: string) =>
    router.get(
        '/exams',
        { [key]: value },
        { preserveState: true, replace: true },
    );
defineOptions({
    layout: { breadcrumbs: [{ title: 'Exams', href: '/exams' }] },
});
</script>
<template>
    <Head title="Exams" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Exams"
            description="Schedule exams, manage subjects, enter marks, and publish results."
            action-label="Add Exam"
            action-href="/exams/create"
        />
        <Card
            ><CardContent class="space-y-4 pt-6">
                <div class="grid gap-3 md:grid-cols-2">
                    <select
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        @change="
                            filter(
                                'academic_session_id',
                                ($event.target as HTMLSelectElement).value,
                            )
                        "
                    >
                        <option value="">All sessions</option>
                        <option
                            v-for="s in options.academicSessions"
                            :key="s.id"
                            :value="s.id"
                        >
                            {{ s.name }}
                        </option>
                    </select>
                    <select
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        @change="
                            filter(
                                'class_id',
                                ($event.target as HTMLSelectElement).value,
                            )
                        "
                    >
                        <option value="">All classes</option>
                        <option
                            v-for="c in options.classes"
                            :key="c.id"
                            :value="c.id"
                        >
                            {{ c.name }}
                        </option>
                    </select>
                </div>
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="py-3">Exam</th>
                            <th>Type</th>
                            <th>Class</th>
                            <th>Session</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="exam in exams"
                            :key="exam.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3 font-medium">{{ exam.title }}</td>
                            <td>{{ exam.exam_type.name }}</td>
                            <td>{{ exam.class_room.name }}</td>
                            <td>{{ exam.academic_session.name }}</td>
                            <td>{{ exam.start_date }} - {{ exam.end_date }}</td>
                            <td>
                                <Badge variant="secondary">{{
                                    exam.status
                                }}</Badge>
                            </td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <Button size="sm" as-child
                                        ><Link :href="`/exams/${exam.id}`"
                                            >Open</Link
                                        ></Button
                                    ><Button
                                        size="sm"
                                        variant="outline"
                                        as-child
                                        ><Link :href="`/exams/${exam.id}/edit`"
                                            >Edit</Link
                                        ></Button
                                    >
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent></Card
        >
    </div>
</template>
