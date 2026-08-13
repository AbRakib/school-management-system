<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Option = { id: number; name: string; school_class_id?: number };
type Row = {
    id: number;
    name: string;
    admission_no: string | null;
    full_class_name: string;
    marked_days: number;
    present_days: number;
    absent_days: number;
    late_days: number;
    percentage: number;
};

const props = defineProps<{
    report: {
        rows: Row[];
        summary: {
            students: number;
            class_average: number;
            marked_days: number;
            present_days: number;
        };
    };
    filters: Record<string, string | number | null>;
    options: { classes: Option[]; sections: Option[] };
}>();

const filters = reactive({
    class_id: props.filters.class_id ?? '',
    section_id: props.filters.section_id ?? '',
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
});

const apply = () =>
    router.get('/reports/attendance', filters, {
        preserveState: true,
        replace: true,
    });
const exportFile = (type: 'pdf' | 'excel') =>
    router.get('/reports/attendance', { ...filters, export: type });

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Attendance Report', href: '/reports/attendance' },
        ],
    },
});
</script>

<template>
    <Head title="Attendance Report" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Attendance Report"
            description="Per-student attendance percentages and class average for the selected range."
        />

        <Card>
            <CardContent class="grid gap-3 pt-6 md:grid-cols-6">
                <select
                    v-model="filters.class_id"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                >
                    <option value="">All classes</option>
                    <option
                        v-for="schoolClass in options.classes"
                        :key="schoolClass.id"
                        :value="schoolClass.id"
                    >
                        {{ schoolClass.name }}
                    </option>
                </select>
                <select
                    v-model="filters.section_id"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                >
                    <option value="">All sections</option>
                    <option
                        v-for="section in options.sections"
                        :key="section.id"
                        :value="section.id"
                    >
                        {{ section.name }}
                    </option>
                </select>
                <Input v-model="filters.start_date" type="date" />
                <Input v-model="filters.end_date" type="date" />
                <Button @click="apply">Apply</Button>
                <div class="flex gap-2">
                    <Button variant="outline" @click="exportFile('excel')">
                        <Download class="size-4" />Excel
                    </Button>
                    <Button variant="outline" @click="exportFile('pdf')"
                        >PDF</Button
                    >
                </div>
            </CardContent>
        </Card>

        <div class="grid gap-4 md:grid-cols-3">
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
                    <p class="text-sm text-muted-foreground">
                        Present / Marked
                    </p>
                    <p class="text-3xl font-semibold">
                        {{ report.summary.present_days }} /
                        {{ report.summary.marked_days }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>Class Average</CardTitle></CardHeader>
            <CardContent>
                <div class="h-4 rounded-full bg-muted">
                    <div
                        class="h-4 rounded-full bg-primary"
                        :style="{ width: `${report.summary.class_average}%` }"
                    />
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Students</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Student</th>
                            <th>Class</th>
                            <th>Marked</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Late</th>
                            <th>Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in report.rows"
                            :key="row.id"
                            class="border-b"
                        >
                            <td class="py-3">
                                <div class="font-medium">{{ row.name }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ row.admission_no }}
                                </div>
                            </td>
                            <td>{{ row.full_class_name }}</td>
                            <td>{{ row.marked_days }}</td>
                            <td>{{ row.present_days }}</td>
                            <td>{{ row.absent_days }}</td>
                            <td>{{ row.late_days }}</td>
                            <td class="font-medium">{{ row.percentage }}%</td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
