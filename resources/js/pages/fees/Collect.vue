<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
const props = defineProps<{
    students: any[];
    selectedStudent: any | null;
    search: string | null;
}>();
const pay = useForm({
    amount: '',
    payment_method: 'cash',
    transaction_ref: '',
});
const searchForm = useForm({ search: props.search ?? '', student_id: '' });
const openStudent = (id: number) =>
    router.get('/fees/collect', { student_id: id, search: searchForm.search });
const submit = (id: number, due: string) => {
    if (!pay.amount) pay.amount = due;
    pay.post(`/fees/invoices/${id}/pay`, {
        onSuccess: () => {
            pay.reset();
            alert(
                'Payment saved. Receipt can be printed from payment history.',
            );
        },
    });
};
defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Fee Collection', href: '/fees/collect' }],
    },
});
</script>
<template>
    <Head title="Fee Collection" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Fee Collection"
            description="Search a student, review pending invoices, and collect payment."
        /><Card
            ><CardContent class="space-y-3 pt-6"
                ><form
                    class="flex gap-2"
                    @submit.prevent="
                        router.get('/fees/collect', {
                            search: searchForm.search,
                        })
                    "
                >
                    <Input
                        v-model="searchForm.search"
                        placeholder="Search by name or admission no"
                    /><Button>Search</Button>
                </form>
                <div class="flex flex-wrap gap-2">
                    <Button
                        v-for="s in students"
                        :key="s.id"
                        variant="outline"
                        @click="openStudent(s.id)"
                        >{{ s.name }} ({{ s.admission_no }})</Button
                    >
                </div></CardContent
            ></Card
        ><Card v-if="selectedStudent"
            ><CardHeader
                ><CardTitle
                    >{{ selectedStudent.name }} Invoices</CardTitle
                ></CardHeader
            ><CardContent class="grid gap-4 md:grid-cols-2"
                ><div
                    v-for="invoice in selectedStudent.fee_invoices"
                    :key="invoice.id"
                    class="rounded-lg border p-4"
                >
                    <div class="flex justify-between">
                        <b>{{ invoice.month || 'One-time' }}</b
                        ><span>{{ invoice.status }}</span>
                    </div>
                    <p class="text-sm text-muted-foreground">
                        Total {{ invoice.total_amount }} · Paid
                        {{ invoice.paid_amount }} · Due {{ invoice.due_amount }}
                    </p>
                    <div
                        v-if="Number(invoice.due_amount) > 0"
                        class="mt-3 grid gap-2"
                    >
                        <Input
                            v-model="pay.amount"
                            type="number"
                            :placeholder="invoice.due_amount"
                        /><select
                            v-model="pay.payment_method"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        >
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                            <option value="mobile_banking">
                                Mobile Banking
                            </option>
                            <option value="card">Card</option></select
                        ><Input
                            v-model="pay.transaction_ref"
                            placeholder="Transaction ref"
                        /><Button
                            @click="submit(invoice.id, invoice.due_amount)"
                            >Pay</Button
                        >
                    </div>
                </div></CardContent
            ></Card
        >
    </div>
</template>
