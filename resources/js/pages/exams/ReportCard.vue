<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Printer } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
defineProps<{ student: any; report: any }>();
const printReport = () => window.print();
defineOptions({
    layout: { breadcrumbs: [{ title: 'Report Card', href: '/exams' }] },
});
</script>
<template>
    <Head title="Report Card" />
    <div class="space-y-6 p-4 md:p-6">
        <div class="no-print flex justify-between">
            <Button variant="outline" as-child
                ><Link href="/exams">Back</Link></Button
            ><Button @click="printReport"
                ><Printer class="size-4" />Print</Button
            >
        </div>
        <Card class="print-card"
            ><CardContent class="space-y-6 pt-6"
                ><div class="text-center">
                    <h1 class="text-2xl font-bold">Report Card</h1>
                    <p class="text-muted-foreground">
                        {{ report.exam.title }} ·
                        {{ report.exam.class_room.name }}
                    </p>
                </div>
                <div class="grid gap-2 text-sm md:grid-cols-2">
                    <p><b>Name:</b> {{ student.name }}</p>
                    <p><b>Admission:</b> {{ student.admission_no }}</p>
                    <p><b>Guardian:</b> {{ student.guardian.name }}</p>
                    <p><b>Section:</b> {{ student.section?.name }}</p>
                </div>
                <table class="w-full text-sm">
                    <thead class="border-b">
                        <tr>
                            <th class="py-2 text-left">Subject</th>
                            <th>Full</th>
                            <th>Pass</th>
                            <th>Marks</th>
                            <th>Grade</th>
                            <th>GP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in report.subjects"
                            :key="row.subject"
                            class="border-b"
                        >
                            <td class="py-2">{{ row.subject }}</td>
                            <td class="text-center">{{ row.full_marks }}</td>
                            <td class="text-center">{{ row.pass_marks }}</td>
                            <td class="text-center">
                                {{ row.marks_obtained ?? '-' }}
                            </td>
                            <td class="text-center">{{ row.grade }}</td>
                            <td class="text-center">{{ row.grade_point }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="grid gap-3 text-center md:grid-cols-4">
                    <div>
                        <p class="text-xs text-muted-foreground">Total</p>
                        <p class="text-xl font-bold">
                            {{ report.total_marks }}/{{ report.full_marks }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Percentage</p>
                        <p class="text-xl font-bold">
                            {{ report.percentage }}%
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Grade/GPA</p>
                        <p class="text-xl font-bold">
                            {{ report.overall_grade }} / {{ report.gpa }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Result</p>
                        <p
                            class="text-xl font-bold"
                            :class="
                                report.passed
                                    ? 'text-emerald-600'
                                    : 'text-rose-600'
                            "
                        >
                            {{ report.passed ? 'PASS' : 'FAIL' }}
                        </p>
                    </div>
                </div></CardContent
            ></Card
        >
    </div>
</template>
<style>
@media print {
    .no-print,
    [data-sidebar='sidebar'],
    header {
        display: none !important;
    }
    .print-card {
        box-shadow: none !important;
        border: 0 !important;
    }
}
</style>
