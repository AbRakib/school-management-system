<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Option = {
    id: number;
    name?: string;
    title?: string;
    class_id?: number;
    status?: string;
};
type Row = {
    id: number;
    rank: number;
    student_name: string;
    admission_no: string | null;
    full_class_name: string;
    total_marks: number;
    full_marks: number;
    percentage: number;
    grade: string;
    status: string;
};

const props = defineProps<{
    report: {
        rows: Row[];
        summary: {
            students: number;
            class_average: number;
            pass_rate: number;
            passed: number;
            failed: number;
        };
    };
    filters: Record<string, string | number | null>;
    options: { classes: Option[]; exams: Option[] };
}>();

const filters = reactive({
    exam_id: props.filters.exam_id ?? '',
    class_id: props.filters.class_id ?? '',
});

const apply = () =>
    router.get('/reports/results', filters, {
        preserveState: true,
        replace: true,
    });
const exportFile = (type: 'pdf' | 'excel') =>
    router.get('/reports/results', { ...filters, export: type });

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Result Report', href: '/reports/results' }],
    },
});
</script>

<template>
    <Head title="Result Report" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Result Report"
            description="Ranked class performance for a selected exam."
        />

        <Card>
            <CardContent class="grid gap-3 pt-6 md:grid-cols-5">
                <select
                    v-model="filters.exam_id"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                >
                    <option value="">Exam</option>
                    <option
                        v-for="exam in options.exams"
                        :key="exam.id"
                        :value="exam.id"
                    >
                        {{ exam.title }} ({{ exam.status }})
                    </option>
                </select>
                <select
                    v-model="filters.class_id"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                >
                    <option value="">Exam class</option>
                    <option
                        v-for="schoolClass in options.classes"
                        :key="schoolClass.id"
                        :value="schoolClass.id"
                    >
                        {{ schoolClass.name }}
                    </option>
                </select>
                <Button @click="apply">Apply</Button>
                <Button variant="outline" @click="exportFile('excel')">
                    <Download class="size-4" />Excel
                </Button>
                <Button variant="outline" @click="exportFile('pdf')"
                    >PDF</Button
                >
            </CardContent>
        </Card>

        <div class="grid gap-4 md:grid-cols-4">
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Students</p>
                    <p class="text-3xl font-semibold">
                        {{ report.summary.students }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Class average</p>
                    <p class="text-3xl font-semibold">
                        {{ report.summary.class_average }}%
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Pass rate</p>
                    <p class="text-3xl font-semibold">
                        {{ report.summary.pass_rate }}%
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Pass / Fail</p>
                    <p class="text-3xl font-semibold">
                        {{ report.summary.passed }} /
                        {{ report.summary.failed }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>Ranked Results</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Rank</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Total</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in report.rows"
                            :key="row.id"
                            class="border-b"
                        >
                            <td class="py-3 font-semibold">{{ row.rank }}</td>
                            <td>
                                <div class="font-medium">
                                    {{ row.student_name }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ row.admission_no }}
                                </div>
                            </td>
                            <td>{{ row.full_class_name }}</td>
                            <td>
                                {{ row.total_marks }} / {{ row.full_marks }}
                            </td>
                            <td>{{ row.percentage }}%</td>
                            <td>{{ row.grade }}</td>
                            <td>
                                <Badge
                                    :variant="
                                        row.status === 'pass'
                                            ? 'secondary'
                                            : 'destructive'
                                    "
                                >
                                    {{ row.status }}
                                </Badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
