<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type SchoolClass = {
    id: number;
    name: string;
    grade_level: number;
};

type Teacher = {
    id: number;
    name: string;
    designation: 'teacher' | 'head_teacher';
};

type Subject = {
    id: number;
    name: string;
    code: string;
    school_class_id: number | null;
    teacher_id: number | null;
    description: string | null;
};

const props = defineProps<{
    subject?: Subject;
    classes: SchoolClass[];
    teachers: Teacher[];
}>();
const editing = Boolean(props.subject);

const form = useForm({
    name: props.subject?.name ?? '',
    code: props.subject?.code ?? '',
    school_class_id: props.subject?.school_class_id ?? '',
    teacher_id: props.subject?.teacher_id ?? '',
    description: props.subject?.description ?? '',
});

const submit = () => {
    if (props.subject) {
        form.put(`/subjects/${props.subject.id}`);

        return;
    }

    form.post('/subjects');
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Subjects', href: '/subjects' }],
    },
});
</script>

<template>
    <Head :title="editing ? 'Edit subject' : 'Add subject'" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="editing ? 'Edit subject' : 'Add subject'"
            description="Enter the subject name, code, class level, and teacher assignment."
        />

        <Card class="max-w-3xl">
            <CardContent class="pt-6">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Subject name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="code">Subject code</Label>
                            <Input id="code" v-model="form.code" required />
                            <InputError :message="form.errors.code" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="school_class_id">Class</Label>
                            <select
                                id="school_class_id"
                                v-model="form.school_class_id"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            >
                                <option value="">Not assigned</option>
                                <option
                                    v-for="schoolClass in classes"
                                    :key="schoolClass.id"
                                    :value="schoolClass.id"
                                >
                                    {{ schoolClass.name }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.school_class_id"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="teacher_id">Teacher</Label>
                            <select
                                id="teacher_id"
                                v-model="form.teacher_id"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            >
                                <option value="">Not assigned</option>
                                <option
                                    v-for="teacher in teachers"
                                    :key="teacher.id"
                                    :value="teacher.id"
                                >
                                    {{ teacher.name }}
                                    {{
                                        teacher.designation === 'head_teacher'
                                            ? '(Head Teacher)'
                                            : ''
                                    }}
                                </option>
                            </select>
                            <InputError :message="form.errors.teacher_id" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="description">Description</Label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                    <div class="flex gap-3">
                        <Button type="submit" :disabled="form.processing">
                            {{ editing ? 'Update subject' : 'Save subject' }}
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/subjects">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
