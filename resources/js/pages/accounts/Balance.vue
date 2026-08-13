<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
const props = defineProps<{
    balance: number;
    history: any[];
    transactions: any[] | { data: any[] };
    filters: Record<string, string | null>;
}>();
const filters = reactive({
    start_date: props.filters.start_date ?? '',
    end_date: props.filters.end_date ?? '',
});
const rows = Array.isArray(props.transactions)
    ? props.transactions
    : props.transactions.data;
const totalIncome = props.history.reduce((s, r) => s + Number(r.income), 0);
const totalExpense = props.history.reduce((s, r) => s + Number(r.expense), 0);
defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Account Balance', href: '/accounts/balance' }],
    },
});
</script>
<template>
    <Head title="Account Balance" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Account Balance"
            description="Balance is derived only from account transaction income minus expense."
        /><Card
            ><CardContent class="grid gap-4 pt-6 md:grid-cols-3"
                ><div>
                    <p class="text-sm text-muted-foreground">Current Balance</p>
                    <p class="text-4xl font-bold">{{ balance }}</p>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">Income in range</p>
                    <p class="text-2xl font-semibold text-emerald-600">
                        {{ totalIncome }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-muted-foreground">
                        Expense in range
                    </p>
                    <p class="text-2xl font-semibold text-rose-600">
                        {{ totalExpense }}
                    </p>
                </div></CardContent
            ></Card
        ><Card
            ><CardHeader><CardTitle>Income vs Expense</CardTitle></CardHeader
            ><CardContent
                ><div class="mb-4 flex gap-2">
                    <Input v-model="filters.start_date" type="date" /><Input
                        v-model="filters.end_date"
                        type="date"
                    /><Button @click="router.get('/accounts/balance', filters)"
                        >Filter</Button
                    >
                </div>
                <div class="flex h-56 items-end gap-2">
                    <div
                        v-for="row in history"
                        :key="row.transaction_date"
                        class="flex flex-1 items-end gap-1"
                    >
                        <div
                            class="w-1/2 bg-emerald-500"
                            :style="{
                                height: `${Math.max((Number(row.income) / (Math.max(totalIncome, totalExpense) || 1)) * 100, 4)}%`,
                            }"
                        ></div>
                        <div
                            class="w-1/2 bg-rose-500"
                            :style="{
                                height: `${Math.max((Number(row.expense) / (Math.max(totalIncome, totalExpense) || 1)) * 100, 4)}%`,
                            }"
                        ></div>
                    </div></div></CardContent></Card
        ><Card
            ><CardHeader><CardTitle>Recent Transactions</CardTitle></CardHeader
            ><CardContent class="overflow-x-auto"
                ><table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Date</th>
                            <th>Type</th>
                            <th>Source</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in rows" :key="t.id" class="border-b">
                            <td class="py-3">{{ t.transaction_date }}</td>
                            <td>{{ t.type }}</td>
                            <td>{{ t.source }}</td>
                            <td>{{ t.description }}</td>
                            <td>{{ t.amount }}</td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
    </div>
</template>
