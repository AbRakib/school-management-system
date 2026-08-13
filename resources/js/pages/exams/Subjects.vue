<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
const props = defineProps<{ exam: any; subjects: any[] }>();
const blank = () => ({
    subject_id: '',
    exam_date: '',
    full_marks: 100,
    pass_marks: 40,
});
const form = useForm({
    subjects: props.exam.subjects.length
        ? props.exam.subjects.map((s: any) => ({
              subject_id: s.subject_id,
              exam_date: s.exam_date ?? '',
              full_marks: s.full_marks,
              pass_marks: s.pass_marks,
          }))
        : [blank()],
});
defineOptions({
    layout: { breadcrumbs: [{ title: 'Exam Subjects', href: '/exams' }] },
});
</script>
<template>
    <Head title="Exam Subjects" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="`${exam.title} Subjects`"
            description="Attach subjects and marks to this exam."
        /><Card
            ><CardContent class="space-y-3 pt-6"
                ><div
                    v-for="(row, i) in form.subjects"
                    :key="i"
                    class="grid gap-3 md:grid-cols-[1fr_160px_120px_120px_auto]"
                >
                    <select
                        v-model="row.subject_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="">Subject</option>
                        <option v-for="s in subjects" :key="s.id" :value="s.id">
                            {{ s.name }} ({{ s.code }})
                        </option></select
                    ><Input v-model="row.exam_date" type="date" /><Input
                        v-model="row.full_marks"
                        type="number"
                    /><Input v-model="row.pass_marks" type="number" /><Button
                        variant="ghost"
                        size="icon"
                        @click="form.subjects.splice(i, 1)"
                        ><Trash2 class="size-4"
                    /></Button>
                </div>
                <div class="flex gap-3">
                    <Button
                        variant="outline"
                        @click="form.subjects.push(blank())"
                        ><Plus class="size-4" />Add</Button
                    ><Button @click="form.post(`/exams/${exam.id}/subjects`)"
                        >Save</Button
                    ><Button variant="outline" as-child
                        ><Link :href="`/exams/${exam.id}`">Back</Link></Button
                    >
                </div></CardContent
            ></Card
        >
    </div>
</template>
