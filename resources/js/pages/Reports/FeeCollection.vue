<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Option = { id: number; name: string };
type Trend = { date: string; amount: number; chart_percentage: number };
type Breakdown = { fee_type: string; amount: number };
type Transaction = {
    id: number;
    date: string;
    student: string | null;
    admission_no: string | null;
    invoice_id: number;
    amount: number;
    method: string;
    reference: string | null;
};

const props = defineProps<{
    report: {
        summary: {
            total_collected: number;
            total_due: number;
            transactions: number;
        };
        by_fee_type: Breakdown[];
        trend: Trend[];
        transactions: Transaction[];
    };
    filters: Record<string, string | number | null>;
    options: { classes: Option[] };
}>();

const filters = reactive({
    class_id: props.filters.class_id ?? '',
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
});

const money = (amount: number) =>
    new Intl.NumberFormat('en-BD', {
        style: 'currency',
        currency: 'BDT',
        maximumFractionDigits: 0,
    }).format(amount);
const apply = () =>
    router.get('/reports/fee-collection', filters, {
        preserveState: true,
        replace: true,
    });
const exportFile = (type: 'pdf' | 'excel') =>
    router.get('/reports/fee-collection', { ...filters, export: type });

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Fee Collection Report', href: '/reports/fee-collection' },
        ],
    },
});
</script>

<template>
    <Head title="Fee Collection Report" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Fee Collection Report"
            description="Collection totals, fee type breakdown, trend, and payment transactions."
        />

        <Card>
            <CardContent class="grid gap-3 pt-6 md:grid-cols-6">
                <Input v-model="filters.start_date" type="date" />
                <Input v-model="filters.end_date" type="date" />
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
                <Button @click="apply">Apply</Button>
                <Button variant="outline" @click="exportFile('excel')">
                    <Download class="size-4" />Excel
                </Button>
                <Button variant="outline" @click="exportFile('pdf')"
                    >PDF</Button
                >
            </CardContent>
        </Card>

        <div class="grid gap-4 md:grid-cols-3">
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Total collected</p>
                    <p class="text-3xl font-semibold">
                        {{ money(report.summary.total_collected) }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Total due</p>
                    <p class="text-3xl font-semibold">
                        {{ money(report.summary.total_due) }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Transactions</p>
                    <p class="text-3xl font-semibold">
                        {{ report.summary.transactions }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Collection Trend</CardTitle></CardHeader>
                <CardContent>
                    <div class="flex h-56 items-end gap-3">
                        <div
                            v-for="item in report.trend"
                            :key="item.date"
                            class="flex flex-1 flex-col items-center gap-2"
                        >
                            <div
                                class="w-full rounded-t-md bg-primary"
                                :style="{ height: `${item.chart_percentage}%` }"
                            />
                            <span class="text-xs text-muted-foreground">{{
                                item.date
                            }}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader
                    ><CardTitle>Fee Type Breakdown</CardTitle></CardHeader
                >
                <CardContent class="space-y-3">
                    <div
                        v-for="item in report.by_fee_type"
                        :key="item.fee_type"
                        class="flex justify-between text-sm"
                    >
                        <span>{{ item.fee_type }}</span>
                        <span class="font-medium">{{
                            money(item.amount)
                        }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>Transactions</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Date</th>
                            <th>Student</th>
                            <th>Invoice</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="row in report.transactions"
                            :key="row.id"
                            class="border-b"
                        >
                            <td class="py-3">{{ row.date }}</td>
                            <td>
                                <div class="font-medium">{{ row.student }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ row.admission_no }}
                                </div>
                            </td>
                            <td>#{{ row.invoice_id }}</td>
                            <td>{{ row.method }}</td>
                            <td>{{ row.reference || '-' }}</td>
                            <td class="text-right font-medium">
                                {{ money(row.amount) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
