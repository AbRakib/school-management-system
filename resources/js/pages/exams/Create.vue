<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Option = { id: number; name: string; code?: string };
const props = defineProps<{
    exam?: any;
    examTypes: Option[];
    academicSessions: Option[];
    classes: Option[];
    subjects: Option[];
    statuses: string[];
}>();
const blank = () => ({
    subject_id: '',
    exam_date: '',
    full_marks: 100,
    pass_marks: 40,
});
const form = useForm({
    exam_type_id: props.exam?.exam_type_id ?? '',
    academic_session_id: props.exam?.academic_session_id ?? '',
    class_id: props.exam?.class_id ?? '',
    title: props.exam?.title ?? '',
    start_date: props.exam?.start_date ?? '',
    end_date: props.exam?.end_date ?? '',
    status: props.exam?.status ?? 'scheduled',
    subjects: props.exam?.subjects?.length
        ? props.exam.subjects.map((s: any) => ({
              subject_id: s.subject_id,
              exam_date: s.exam_date ?? '',
              full_marks: s.full_marks,
              pass_marks: s.pass_marks,
          }))
        : [blank()],
});
const add = () => form.subjects.push(blank());
const submit = () =>
    props.exam ? form.put(`/exams/${props.exam.id}`) : form.post('/exams');
defineOptions({
    layout: { breadcrumbs: [{ title: 'Exams', href: '/exams' }] },
});
</script>
<template>
    <Head :title="exam ? 'Edit Exam' : 'Add Exam'" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="exam ? 'Edit Exam' : 'Add Exam'"
            description="Set exam basics and attach subject papers."
        />
        <form class="space-y-6" @submit.prevent="submit">
            <Card
                ><CardHeader><CardTitle>Exam Info</CardTitle></CardHeader
                ><CardContent class="grid gap-4 md:grid-cols-3">
                    <select
                        v-model="form.exam_type_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        required
                    >
                        <option value="">Type</option>
                        <option
                            v-for="x in examTypes"
                            :key="x.id"
                            :value="x.id"
                        >
                            {{ x.name }}
                        </option>
                    </select>
                    <select
                        v-model="form.academic_session_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        required
                    >
                        <option value="">Session</option>
                        <option
                            v-for="x in academicSessions"
                            :key="x.id"
                            :value="x.id"
                        >
                            {{ x.name }}
                        </option>
                    </select>
                    <select
                        v-model="form.class_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        required
                    >
                        <option value="">Class</option>
                        <option v-for="x in classes" :key="x.id" :value="x.id">
                            {{ x.name }}
                        </option>
                    </select>
                    <Input
                        v-model="form.title"
                        placeholder="Exam title"
                        required
                    /><Input
                        v-model="form.start_date"
                        type="date"
                        required
                    /><Input v-model="form.end_date" type="date" required />
                    <select
                        v-model="form.status"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option v-for="s in statuses" :key="s" :value="s">
                            {{ s }}
                        </option>
                    </select>
                </CardContent></Card
            >
            <Card
                ><CardHeader class="flex-row items-center justify-between"
                    ><CardTitle>Subjects</CardTitle
                    ><Button type="button" variant="outline" @click="add"
                        ><Plus class="size-4" />Add</Button
                    ></CardHeader
                ><CardContent class="space-y-3">
                    <div
                        v-for="(row, i) in form.subjects"
                        :key="i"
                        class="grid gap-3 md:grid-cols-[1fr_160px_120px_120px_auto]"
                    >
                        <select
                            v-model="row.subject_id"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                            required
                        >
                            <option value="">Subject</option>
                            <option
                                v-for="s in subjects"
                                :key="s.id"
                                :value="s.id"
                            >
                                {{ s.name }} {{ s.code ? `(${s.code})` : '' }}
                            </option>
                        </select>
                        <Input v-model="row.exam_date" type="date" /><Input
                            v-model="row.full_marks"
                            type="number"
                        /><Input
                            v-model="row.pass_marks"
                            type="number"
                        /><Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="form.subjects.splice(i, 1)"
                            ><Trash2 class="size-4"
                        /></Button>
                    </div> </CardContent
            ></Card>
            <div class="flex gap-3">
                <Button>{{ exam ? 'Update' : 'Create' }}</Button
                ><Button variant="outline" as-child
                    ><Link href="/exams">Cancel</Link></Button
                >
            </div>
        </form>
    </div>
</template>
