<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Plus, Trash2 } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Option = {
    id: number;
    name: string;
    code?: string;
    school_class_id?: number;
};
type Assignment = {
    id: number;
    class_id: number;
    section_id: number;
    subject_id: number;
    academic_session_id: number;
};
type AssignmentFormRow = {
    class_id: number | string;
    section_id: number | string;
    subject_id: number | string;
    academic_session_id: number | string;
};
type Staff = {
    id: number;
    name: string;
    staff_id: string;
    assignments: Assignment[];
};

const props = defineProps<{
    staffMember: Staff;
    options: {
        classes: Option[];
        sections: Option[];
        subjects: Option[];
        academicSessions: Option[];
    };
}>();

const blankRow = (): AssignmentFormRow => ({
    class_id: '',
    section_id: '',
    subject_id: '',
    academic_session_id: '',
});

const form = useForm({
    assignments: props.staffMember.assignments.length
        ? props.staffMember.assignments.map(
              (item): AssignmentFormRow => ({
                  class_id: item.class_id,
                  section_id: item.section_id,
                  subject_id: item.subject_id,
                  academic_session_id: item.academic_session_id,
              }),
          )
        : [blankRow()],
});

const addRow = () => form.assignments.push(blankRow());
const removeRow = (index: number) => {
    form.assignments.splice(index, 1);
    if (form.assignments.length === 0) addRow();
};
const submit = () => form.post(`/staff/${props.staffMember.id}/assignments`);

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Staff Assignments', href: '/staff' }],
    },
});
</script>

<template>
    <Head :title="`${staffMember.name} Assignments`" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="`${staffMember.name} Assignments`"
            :description="`${staffMember.staff_id} class and subject assignments.`"
        />

        <Card>
            <CardHeader class="flex-row items-center justify-between space-y-0">
                <CardTitle>Class & Subject Assignment</CardTitle>
                <Button type="button" variant="outline" @click="addRow">
                    <Plus class="size-4" />
                    Add row
                </Button>
            </CardHeader>
            <CardContent>
                <InputError :message="form.errors.assignments" class="mb-3" />
                <form class="space-y-4" @submit.prevent="submit">
                    <div
                        v-for="(row, index) in form.assignments"
                        :key="index"
                        class="grid gap-3 rounded-lg border p-3 md:grid-cols-[1fr_1fr_1fr_1fr_auto]"
                    >
                        <select
                            v-model="row.class_id"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            required
                        >
                            <option value="" disabled>Class</option>
                            <option
                                v-for="item in options.classes"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                        <select
                            v-model="row.section_id"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            required
                        >
                            <option value="" disabled>Section</option>
                            <option
                                v-for="item in options.sections"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                        <select
                            v-model="row.subject_id"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            required
                        >
                            <option value="" disabled>Subject</option>
                            <option
                                v-for="item in options.subjects"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }} ({{ item.code }})
                            </option>
                        </select>
                        <select
                            v-model="row.academic_session_id"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            required
                        >
                            <option value="" disabled>Session</option>
                            <option
                                v-for="item in options.academicSessions"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removeRow(index)"
                        >
                            <Trash2 class="size-4" />
                        </Button>
                    </div>
                    <div class="flex gap-3">
                        <Button type="submit" :disabled="form.processing"
                            >Save assignments</Button
                        >
                        <Button variant="outline" as-child
                            ><Link :href="`/staff/${staffMember.id}`"
                                >Cancel</Link
                            ></Button
                        >
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
