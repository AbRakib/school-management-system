<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Download } from '@lucide/vue';
import { reactive, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Option = { id: number; name: string; school_class_id?: number };
type StudentRow = {
    id: number;
    admission_no: string;
    name: string;
    roll_no: string | null;
    image_url: string;
    status: string;
    remarks: string;
};

const props = defineProps<{
    students: StudentRow[];
    filters: Record<string, string | null>;
    options: {
        classes: Option[];
        sections: Option[];
        academicSessions: Option[];
    };
    statuses: string[];
}>();

const filters = reactive({
    class_id: props.filters.class_id ?? '',
    section_id: props.filters.section_id ?? '',
    academic_session_id: props.filters.academic_session_id ?? '',
    date: props.filters.date ?? new Date().toISOString().slice(0, 10),
});

const form = useForm({
    class_id: filters.class_id,
    section_id: filters.section_id,
    academic_session_id: filters.academic_session_id,
    date: filters.date,
    attendances: props.students.map((student) => ({
        student_id: student.id,
        status: student.status,
        remarks: student.remarks ?? '',
    })),
});

watch(
    () => props.students,
    (students) => {
        form.attendances = students.map((student) => ({
            student_id: student.id,
            status: student.status,
            remarks: student.remarks ?? '',
        }));
    },
);

const loadStudents = () => {
    router.get('/attendance/students', filters, {
        preserveState: true,
        replace: true,
    });
};

const markAllPresent = () => {
    form.attendances.forEach((attendance) => {
        attendance.status = 'present';
    });
};

const save = () => {
    form.class_id = filters.class_id;
    form.section_id = filters.section_id;
    form.academic_session_id = filters.academic_session_id;
    form.date = filters.date;
    form.post('/attendance/students');
};

const statusClass = (active: boolean, status: string) =>
    active
        ? {
              present: 'bg-emerald-600 text-white',
              absent: 'bg-rose-600 text-white',
              late: 'bg-amber-500 text-white',
              half_day: 'bg-sky-600 text-white',
          }[status]
        : 'bg-muted text-muted-foreground hover:bg-muted/80';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Student Attendance', href: '/attendance/students' },
        ],
    },
});
</script>

<template>
    <Head title="Student Attendance" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Student Attendance"
            description="Load a class roster and save the whole attendance sheet in one click."
        />

        <Card>
            <CardContent class="grid gap-3 pt-6 md:grid-cols-5">
                <select
                    v-model="filters.class_id"
                    class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                    @change="loadStudents"
                >
                    <option value="">Select class</option>
                    <option
                        v-for="item in options.classes"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
                <select
                    v-model="filters.section_id"
                    class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                    @change="loadStudents"
                >
                    <option value="">Select section</option>
                    <option
                        v-for="item in options.sections"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
                <select
                    v-model="filters.academic_session_id"
                    class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                    @change="loadStudents"
                >
                    <option value="">Select session</option>
                    <option
                        v-for="item in options.academicSessions"
                        :key="item.id"
                        :value="item.id"
                    >
                        {{ item.name }}
                    </option>
                </select>
                <Input
                    v-model="filters.date"
                    type="date"
                    @change="loadStudents"
                />
                <Button type="button" variant="outline" @click="markAllPresent">
                    <CheckCircle2 class="size-4" />
                    Mark All Present
                </Button>
            </CardContent>
        </Card>

        <Card>
            <CardHeader class="flex-row items-center justify-between space-y-0">
                <CardTitle>Students</CardTitle>
                <Button variant="outline" as-child>
                    <a href="/attendance/students/report">
                        <Download class="size-4" />
                        Report
                    </a>
                </Button>
            </CardHeader>
            <CardContent class="space-y-3">
                <InputError :message="form.errors.attendances" />
                <div
                    v-for="(student, index) in students"
                    :key="student.id"
                    class="grid gap-3 rounded-lg border p-3 lg:grid-cols-[1fr_auto_220px] lg:items-center"
                >
                    <div class="flex items-center gap-3">
                        <Avatar class="size-11 border"
                            ><AvatarImage
                                :src="student.image_url"
                                :alt="student.name"
                                class="object-cover"
                        /></Avatar>
                        <div>
                            <p class="font-medium">{{ student.name }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ student.admission_no }} · Roll
                                {{ student.roll_no || 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="status in statuses"
                            :key="status"
                            type="button"
                            :class="[
                                'rounded-md px-3 py-1.5 text-xs font-medium capitalize transition',
                                statusClass(
                                    form.attendances[index].status === status,
                                    status,
                                ),
                            ]"
                            @click="form.attendances[index].status = status"
                        >
                            {{ status.replace('_', ' ') }}
                        </button>
                    </div>
                    <Input
                        v-model="form.attendances[index].remarks"
                        placeholder="Remarks"
                    />
                </div>
                <p
                    v-if="students.length === 0"
                    class="py-12 text-center text-sm text-muted-foreground"
                >
                    Select class, section, session, and date to load students.
                </p>
                <div
                    v-if="students.length"
                    class="sticky bottom-4 flex justify-end"
                >
                    <Button :disabled="form.processing" @click="save"
                        >Save Attendance</Button
                    >
                </div>
            </CardContent>
        </Card>
    </div>
</template>
