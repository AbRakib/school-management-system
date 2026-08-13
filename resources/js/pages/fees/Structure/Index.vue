<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
type Option = { id: number; name: string };
type FeeType = { id: number; name: string; is_recurring: boolean };
type Structure = {
    id: number;
    amount: string;
    due_day: number | null;
    class_room: Option;
    fee_type: FeeType;
    academic_session: Option;
};
defineProps<{
    feeTypes: FeeType[];
    structures: Structure[];
    options: { classes: Option[]; sessions: Option[] };
}>();
const typeForm = useForm({ name: '', is_recurring: false });
const form = useForm({
    class_id: '',
    fee_type_id: '',
    academic_session_id: '',
    amount: '',
    due_day: '',
});
const generate = useForm({
    class_id: '',
    academic_session_id: '',
    month: new Date().toISOString().slice(0, 7),
});
defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Fee Structure', href: '/fee-structures' }],
    },
});
</script>
<template>
    <Head title="Fee Structure" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Fee Structure"
            description="Manage fee types and class-wise fee structures."
        />
        <div class="grid gap-6 xl:grid-cols-2">
            <Card
                ><CardHeader><CardTitle>Fee Types</CardTitle></CardHeader
                ><CardContent class="space-y-3"
                    ><form
                        class="grid gap-3 md:grid-cols-[1fr_auto_auto]"
                        @submit.prevent="
                            typeForm.post('/fee-types', {
                                onSuccess: () => typeForm.reset(),
                            })
                        "
                    >
                        <Input
                            v-model="typeForm.name"
                            placeholder="Tuition Fee"
                        /><label class="flex items-center gap-2 text-sm"
                            ><input
                                v-model="typeForm.is_recurring"
                                type="checkbox"
                            />Recurring</label
                        ><Button>Add</Button>
                    </form>
                    <div
                        v-for="type in feeTypes"
                        :key="type.id"
                        class="flex items-center justify-between border-b py-2"
                    >
                        <span>{{ type.name }}</span
                        ><span class="text-sm text-muted-foreground">{{
                            type.is_recurring ? 'Recurring' : 'One-time'
                        }}</span>
                    </div></CardContent
                ></Card
            >
            <Card
                ><CardHeader
                    ><CardTitle
                        >Generate Monthly Invoices</CardTitle
                    ></CardHeader
                ><CardContent
                    ><form
                        class="grid gap-3"
                        @submit.prevent="
                            generate.post('/fees/generate-invoices')
                        "
                    >
                        <select
                            v-model="generate.class_id"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        >
                            <option value="">Class</option>
                            <option
                                v-for="c in options.classes"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option></select
                        ><select
                            v-model="generate.academic_session_id"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        >
                            <option value="">Session</option>
                            <option
                                v-for="s in options.sessions"
                                :key="s.id"
                                :value="s.id"
                            >
                                {{ s.name }}
                            </option></select
                        ><Input v-model="generate.month" type="month" /><Button
                            >Generate</Button
                        >
                    </form></CardContent
                ></Card
            >
        </div>
        <Card
            ><CardHeader><CardTitle>Structures</CardTitle></CardHeader
            ><CardContent class="space-y-4"
                ><form
                    class="grid gap-3 md:grid-cols-6"
                    @submit.prevent="
                        form.post('/fee-structures', {
                            onSuccess: () => form.reset(),
                        })
                    "
                >
                    <select
                        v-model="form.class_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="">Class</option>
                        <option
                            v-for="c in options.classes"
                            :key="c.id"
                            :value="c.id"
                        >
                            {{ c.name }}
                        </option></select
                    ><select
                        v-model="form.academic_session_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="">Session</option>
                        <option
                            v-for="s in options.sessions"
                            :key="s.id"
                            :value="s.id"
                        >
                            {{ s.name }}
                        </option></select
                    ><select
                        v-model="form.fee_type_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="">Fee Type</option>
                        <option v-for="t in feeTypes" :key="t.id" :value="t.id">
                            {{ t.name }}
                        </option></select
                    ><Input
                        v-model="form.amount"
                        type="number"
                        placeholder="Amount"
                    /><Input
                        v-model="form.due_day"
                        type="number"
                        placeholder="Due day"
                    /><Button>Save</Button>
                </form>
                <table class="w-full text-sm">
                    <tbody>
                        <tr
                            v-for="s in structures"
                            :key="s.id"
                            class="border-b"
                        >
                            <td class="py-3">{{ s.class_room.name }}</td>
                            <td>{{ s.academic_session.name }}</td>
                            <td>{{ s.fee_type.name }}</td>
                            <td>{{ s.amount }}</td>
                            <td>{{ s.due_day }}</td>
                            <td class="text-right">
                                <Button
                                    variant="ghost"
                                    @click="
                                        router.delete(`/fee-structures/${s.id}`)
                                    "
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
