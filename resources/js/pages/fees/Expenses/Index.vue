<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
defineProps<{
    expenses: any[];
    categories: any[];
    filters: Record<string, string | null>;
}>();
const cat = useForm({ name: '' });
const form = useForm({
    expense_category_id: '',
    amount: '',
    description: '',
    expense_date: new Date().toISOString().slice(0, 10),
});
defineOptions({
    layout: { breadcrumbs: [{ title: 'Expenses', href: '/expenses' }] },
});
</script>
<template>
    <Head title="Expenses" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Expenses"
            description="Record expenses and automatically write matching ledger transactions."
        />
        <div class="grid gap-6 xl:grid-cols-3">
            <Card
                ><CardHeader><CardTitle>Categories</CardTitle></CardHeader
                ><CardContent class="space-y-3"
                    ><form
                        class="flex gap-2"
                        @submit.prevent="
                            cat.post('/expense-categories', {
                                onSuccess: () => cat.reset(),
                            })
                        "
                    >
                        <Input
                            v-model="cat.name"
                            placeholder="Category"
                        /><Button>Add</Button>
                    </form>
                    <div
                        v-for="c in categories"
                        :key="c.id"
                        class="border-b py-2"
                    >
                        {{ c.name }}
                    </div></CardContent
                ></Card
            ><Card class="xl:col-span-2"
                ><CardHeader><CardTitle>Add Expense</CardTitle></CardHeader
                ><CardContent
                    ><form
                        class="grid gap-3 md:grid-cols-2"
                        @submit.prevent="
                            form.post('/expenses', {
                                onSuccess: () => form.reset(),
                            })
                        "
                    >
                        <select
                            v-model="form.expense_category_id"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        >
                            <option value="">Category</option>
                            <option
                                v-for="c in categories"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option></select
                        ><Input
                            v-model="form.amount"
                            type="number"
                            placeholder="Amount"
                        /><Input
                            v-model="form.expense_date"
                            type="date"
                        /><Input
                            v-model="form.description"
                            placeholder="Description"
                        /><Button>Save Expense</Button>
                    </form></CardContent
                ></Card
            >
        </div>
        <Card
            ><CardContent class="overflow-x-auto pt-6"
                ><table class="w-full min-w-160 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="e in expenses" :key="e.id" class="border-b">
                            <td class="py-3">{{ e.expense_date }}</td>
                            <td>{{ e.category.name }}</td>
                            <td>{{ e.description }}</td>
                            <td>{{ e.amount }}</td>
                            <td class="text-right">
                                <Button
                                    variant="ghost"
                                    @click="router.delete(`/expenses/${e.id}`)"
                                    >Delete</Button
                                >
                            </td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
    </div>
</template>
