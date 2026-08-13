<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Option = {
    id: number;
    name: string;
    admission_no?: string;
    school_class_id?: number;
};
type Row = {
    id: number;
    name: string;
    admission_no: string;
    full_class_name: string;
    marked_days: number;
    present_days: number;
    absent_days: number;
    late_days: number;
    percentage: number;
};

const props = defineProps<{
    rows: Row[];
    filters: Record<string, string | null>;
    options: { classes: Option[]; sections: Option[]; students: Option[] };
}>();

const filters = reactive({
    class_id: props.filters.class_id ?? '',
    section_id: props.filters.section_id ?? '',
    student_id: props.filters.student_id ?? '',
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
});

const applyFilters = () =>
    router.get('/attendance/students/report', filters, {
        preserveState: true,
        replace: true,
    });
const exportFile = (type: 'Excel' | 'PDF') =>
    alert(`${type} export will be connected when server-side export is added.`);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Attendance Reports',
                href: '/attendance/students/report',
            },
        ],
    },
});
</script>

<template>
    <Head title="Student Attendance Report" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Attendance Reports"
            description="Review attendance percentages and marked-day summaries."
        />

        <div class="flex gap-2">
            <Button>Student Report</Button>
            <Button variant="outline" as-child
                ><Link href="/attendance/staff/report"
                    >Staff Report</Link
                ></Button
            >
        </div>

        <Card>
            <CardContent class="grid gap-3 pt-6 md:grid-cols-6">
                <select
                    v-model="filters.class_id"
                    class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">All classes</option>
                    <option
                        v-for="item in options.classes"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
                <select
                    v-model="filters.section_id"
                    class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">All sections</option>
                    <option
                        v-for="item in options.sections"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
                <select
                    v-model="filters.student_id"
                    class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">All students</option>
                    <option
                        v-for="item in options.students"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }} ({{ item.admission_no }})
                    </option>
                </select>
                <Input
                    v-model="filters.start_date"
                    type="date"
                    @change="applyFilters"
                />
                <Input
                    v-model="filters.end_date"
                    type="date"
                    @change="applyFilters"
                />
                <div class="flex gap-2">
                    <Button variant="outline" @click="exportFile('Excel')"
                        ><Download class="size-4" />Excel</Button
                    >
                    <Button variant="outline" @click="exportFile('PDF')"
                        >PDF</Button
                    >
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Student Report</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-200 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">Student</th>
                            <th class="px-3 py-3 font-medium">Class</th>
                            <th class="px-3 py-3 font-medium">Marked</th>
                            <th class="px-3 py-3 font-medium">Present</th>
                            <th class="px-3 py-3 font-medium">Absent</th>
                            <th class="px-3 py-3 font-medium">Late</th>
                            <th class="px-3 py-3 font-medium">Attendance</th>
                            <th class="px-3 py-3 font-medium">Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in rows"
                            :key="row.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-3 py-4">
                                <div class="font-medium">{{ row.name }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ row.admission_no }}
                                </div>
                            </td>
                            <td class="px-3 py-4">{{ row.full_class_name }}</td>
                            <td class="px-3 py-4">{{ row.marked_days }}</td>
                            <td class="px-3 py-4">{{ row.present_days }}</td>
                            <td class="px-3 py-4">{{ row.absent_days }}</td>
                            <td class="px-3 py-4">{{ row.late_days }}</td>
                            <td class="px-3 py-4 font-medium">
                                {{ row.percentage }}%
                            </td>
                            <td class="px-3 py-4">
                                <div class="h-2 w-32 rounded-full bg-muted">
                                    <div
                                        class="h-2 rounded-full bg-primary"
                                        :style="{ width: `${row.percentage}%` }"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
