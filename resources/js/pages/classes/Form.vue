<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Teacher = {
    id: number;
    name: string;
    designation: 'teacher' | 'head_teacher';
};

type SchoolClass = {
    id: number;
    grade_level: number;
    name: string;
    section: string | null;
    room: string | null;
    class_teacher_id: number | null;
};

const props = defineProps<{
    schoolClass?: SchoolClass;
    teachers: Teacher[];
}>();
const editing = Boolean(props.schoolClass);

const form = useForm({
    grade_level: props.schoolClass?.grade_level ?? 1,
    name: props.schoolClass?.name ?? 'Class 1',
    section: props.schoolClass?.section ?? 'A',
    room: props.schoolClass?.room ?? '',
    class_teacher_id: props.schoolClass?.class_teacher_id ?? '',
});

const updateClassName = () => {
    if (!editing || /^Class \d+$/.test(form.name)) {
        form.name = `Class ${form.grade_level}`;
    }
};

const submit = () => {
    if (props.schoolClass) {
        form.put(`/classes/${props.schoolClass.id}`);

        return;
    }

    form.post('/classes');
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Classes', href: '/classes' }],
    },
});
</script>

<template>
    <Head :title="editing ? 'Edit class' : 'Add class'" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="editing ? 'Edit class' : 'Add class'"
            description="Configure the grade, room, section/group, and class teacher."
        />

        <Card class="max-w-3xl">
            <CardContent class="pt-6">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="grade_level">Grade level</Label>
                            <select
                                id="grade_level"
                                v-model="form.grade_level"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                @change="updateClassName"
                            >
                                <option
                                    v-for="grade in 10"
                                    :key="grade"
                                    :value="grade"
                                >
                                    Class {{ grade }}
                                </option>
                            </select>
                            <InputError :message="form.errors.grade_level" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="name">Display name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="section">Section / Group</Label>
                            <Input id="section" v-model="form.section" />
                            <InputError :message="form.errors.section" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="room">Room</Label>
                            <Input id="room" v-model="form.room" />
                            <InputError :message="form.errors.room" />
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="class_teacher_id">Class teacher</Label>
                            <select
                                id="class_teacher_id"
                                v-model="form.class_teacher_id"
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
                            <InputError
                                :message="form.errors.class_teacher_id"
                            />
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <Button type="submit" :disabled="form.processing">
                            {{ editing ? 'Update class' : 'Save class' }}
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/classes">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
