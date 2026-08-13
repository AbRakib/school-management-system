<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Option = { id: number; name: string; staff_id?: string };
type Row = {
    id: number;
    staff_id: string;
    name: string;
    designation: string | null;
    marked_days: number;
    present_days: number;
    absent_days: number;
    leave_days: number;
    percentage: number;
};

const props = defineProps<{
    rows: Row[];
    filters: Record<string, string | null>;
    options: { staff: Option[] };
}>();

const filters = reactive({
    staff_id: props.filters.staff_id ?? '',
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
});

const applyFilters = () =>
    router.get('/attendance/staff/report', filters, {
        preserveState: true,
        replace: true,
    });
const exportFile = (type: 'Excel' | 'PDF') =>
    alert(`${type} export will be connected when server-side export is added.`);

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Attendance Reports', href: '/attendance/staff/report' },
        ],
    },
});
</script>

<template>
    <Head title="Staff Attendance Report" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Attendance Reports"
            description="Review staff attendance percentages and day summaries."
        />

        <div class="flex gap-2">
            <Button variant="outline" as-child
                ><Link href="/attendance/students/report"
                    >Student Report</Link
                ></Button
            >
            <Button>Staff Report</Button>
        </div>

        <Card>
            <CardContent class="grid gap-3 pt-6 md:grid-cols-4">
                <select
                    v-model="filters.staff_id"
                    class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                    @change="applyFilters"
                >
                    <option value="">All staff</option>
                    <option
                        v-for="item in options.staff"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }} ({{ item.staff_id }})
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
            <CardHeader><CardTitle>Staff Report</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">Staff</th>
                            <th class="px-3 py-3 font-medium">Designation</th>
                            <th class="px-3 py-3 font-medium">Marked</th>
                            <th class="px-3 py-3 font-medium">Present</th>
                            <th class="px-3 py-3 font-medium">Absent</th>
                            <th class="px-3 py-3 font-medium">Leave</th>
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
                                    {{ row.staff_id }}
                                </div>
                            </td>
                            <td class="px-3 py-4">{{ row.designation }}</td>
                            <td class="px-3 py-4">{{ row.marked_days }}</td>
                            <td class="px-3 py-4">{{ row.present_days }}</td>
                            <td class="px-3 py-4">{{ row.absent_days }}</td>
                            <td class="px-3 py-4">{{ row.leave_days }}</td>
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
