<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
const props = defineProps<{ exam: any; examSubject: any; students: any[] }>();
const form = useForm({
    results: props.students.map((s) => ({
        student_id: s.id,
        marks_obtained: s.marks_obtained ?? '',
        remarks: s.remarks ?? '',
    })),
});
const estimate = (marks: number | string) => {
    const pct =
        (Number(marks || 0) / Number(props.examSubject.full_marks)) * 100;
    if (pct >= 80) return 'A+';
    if (pct >= 70) return 'A';
    if (pct >= 60) return 'B';
    if (pct >= 50) return 'C';
    if (pct >= 40) return 'D';
    return 'F';
};
const markAbsentZero = () =>
    form.results.forEach((r) => {
        if (r.marks_obtained === '' || r.marks_obtained === null)
            r.marks_obtained = 0;
    });
defineOptions({
    layout: { breadcrumbs: [{ title: 'Marks Entry', href: '/exams' }] },
});
</script>
<template>
    <Head title="Marks Entry" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="`${examSubject.subject.name} Marks`"
            :description="`${exam.title} · ${exam.class_room.name}`"
        />
        <div class="flex gap-2">
            <Button variant="outline" @click="markAbsentZero"
                >Mark All Absent as 0</Button
            ><Button
                :disabled="form.processing"
                @click="
                    form.post(`/exams/${exam.id}/marks-entry/${examSubject.id}`)
                "
                >Save Marks</Button
            >
        </div>
        <Card
            ><CardContent class="overflow-x-auto pt-6"
                ><table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="py-3">Student</th>
                            <th>Marks</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(student, i) in students"
                            :key="student.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3">
                                <div class="font-medium">
                                    {{ student.name }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ student.admission_no }}
                                </div>
                            </td>
                            <td>
                                <Input
                                    v-model="form.results[i].marks_obtained"
                                    type="number"
                                    :max="examSubject.full_marks"
                                    class="w-32"
                                />
                            </td>
                            <td class="font-medium">
                                {{ estimate(form.results[i].marks_obtained) }}
                            </td>
                            <td><Input v-model="form.results[i].remarks" /></td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-4">
                    <Button variant="outline" as-child
                        ><Link :href="`/exams/${exam.id}`">Back</Link></Button
                    >
                </div></CardContent
            ></Card
        >
    </div>
</template>
