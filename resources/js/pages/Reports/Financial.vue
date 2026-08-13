<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Download } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Category = { category: string; amount: number; chart_percentage: number };
type Trend = {
    transaction_date: string;
    income: string | number;
    expense: string | number;
    income_chart_percentage: number;
    expense_chart_percentage: number;
};
type Transaction = {
    id: number;
    date: string;
    type: string;
    source: string;
    description: string;
    amount: number;
};

const props = defineProps<{
    report: {
        summary: {
            income: number;
            expense: number;
            net_change: number;
            current_balance: number;
        };
        income_by_category: Category[];
        expense_by_category: Category[];
        trend: Trend[];
        transactions: Transaction[];
    };
    filters: Record<string, string | number | null>;
}>();

const filters = reactive({
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
    router.get('/reports/financial', filters, {
        preserveState: true,
        replace: true,
    });
const exportFile = (type: 'pdf' | 'excel') =>
    router.get('/reports/financial', { ...filters, export: type });

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Financial Report', href: '/reports/financial' },
        ],
    },
});
</script>

<template>
    <Head title="Financial Report" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Financial Report"
            description="Income, expenses, category breakdown, and net balance change."
        />

        <Card>
            <CardContent class="grid gap-3 pt-6 md:grid-cols-5">
                <Input v-model="filters.start_date" type="date" />
                <Input v-model="filters.end_date" type="date" />
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
                    <p class="text-sm text-muted-foreground">Income</p>
                    <p class="text-3xl font-semibold">
                        {{ money(report.summary.income) }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Expense</p>
                    <p class="text-3xl font-semibold">
                        {{ money(report.summary.expense) }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Net change</p>
                    <p class="text-3xl font-semibold">
                        {{ money(report.summary.net_change) }}
                    </p>
                </CardContent>
            </Card>
            <Card>
                <CardContent>
                    <p class="text-sm text-muted-foreground">Current balance</p>
                    <p class="text-3xl font-semibold">
                        {{ money(report.summary.current_balance) }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <Card>
                <CardHeader
                    ><CardTitle>Income vs Expense</CardTitle></CardHeader
                >
                <CardContent class="space-y-4">
                    <div
                        v-for="item in report.trend"
                        :key="item.transaction_date"
                        class="space-y-2"
                    >
                        <div class="text-xs text-muted-foreground">
                            {{ item.transaction_date }}
                        </div>
                        <div class="grid gap-2">
                            <div class="h-3 rounded-full bg-muted">
                                <div
                                    class="h-3 rounded-full bg-emerald-500"
                                    :style="{
                                        width: `${item.income_chart_percentage}%`,
                                    }"
                                />
                            </div>
                            <div class="h-3 rounded-full bg-muted">
                                <div
                                    class="h-3 rounded-full bg-rose-500"
                                    :style="{
                                        width: `${item.expense_chart_percentage}%`,
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader
                    ><CardTitle>Category Breakdown</CardTitle></CardHeader
                >
                <CardContent class="space-y-5">
                    <div>
                        <p class="mb-3 text-sm font-medium">Income</p>
                        <div
                            v-for="item in report.income_by_category"
                            :key="item.category"
                            class="space-y-1"
                        >
                            <div class="flex justify-between text-sm">
                                <span>{{ item.category }}</span>
                                <span>{{ money(item.amount) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-muted">
                                <div
                                    class="h-2 rounded-full bg-emerald-500"
                                    :style="{
                                        width: `${item.chart_percentage}%`,
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="mb-3 text-sm font-medium">Expense</p>
                        <div
                            v-for="item in report.expense_by_category"
                            :key="item.category"
                            class="space-y-1"
                        >
                            <div class="flex justify-between text-sm">
                                <span>{{ item.category }}</span>
                                <span>{{ money(item.amount) }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-muted">
                                <div
                                    class="h-2 rounded-full bg-rose-500"
                                    :style="{
                                        width: `${item.chart_percentage}%`,
                                    }"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader><CardTitle>Ledger Transactions</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Date</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Description</th>
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
                            <td>{{ row.type }}</td>
                            <td>{{ row.source }}</td>
                            <td>{{ row.description }}</td>
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
