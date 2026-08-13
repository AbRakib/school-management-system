<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import ProfileImageInput from '@/components/ProfileImageInput.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Option = {
    id: number;
    name: string;
    phone?: string;
    relation?: string;
    school_class_id?: number;
    status?: string;
};
type Student = {
    id: number;
    admission_no: string | null;
    class_id: number | null;
    section_id: number | null;
    academic_session_id: number | null;
    roll_no: string | null;
    guardian_id: number | null;
    student_category_id: number | null;
    name: string;
    dob: string | null;
    gender: string | null;
    blood_group: string | null;
    photo: string | null;
    image_url: string;
    religion: string | null;
    present_address: string | null;
    permanent_address: string | null;
    admission_date: string;
    status: string;
};

const props = defineProps<{
    student?: Student | null;
    classes: Option[];
    sections: Option[];
    academicSessions: Option[];
    guardians: Option[];
    categories: Option[];
    statuses: string[];
}>();

const editing = Boolean(props.student);
const today = new Date().toISOString().slice(0, 10);
const form = useForm({
    admission_no: props.student?.admission_no ?? '',
    class_id: props.student?.class_id ?? '',
    section_id: props.student?.section_id ?? '',
    academic_session_id: props.student?.academic_session_id ?? '',
    roll_no: props.student?.roll_no ?? '',
    guardian_mode: 'existing',
    guardian_id: props.student?.guardian_id ?? '',
    guardian: {
        name: '',
        phone: '',
        email: '',
        relation: 'father',
        occupation: '',
        address: '',
    },
    student_category_id: props.student?.student_category_id ?? '',
    name: props.student?.name ?? '',
    dob: props.student?.dob ?? '',
    gender: props.student?.gender ?? '',
    blood_group: props.student?.blood_group ?? '',
    photo: null as File | null,
    religion: props.student?.religion ?? '',
    present_address: props.student?.present_address ?? '',
    permanent_address: props.student?.permanent_address ?? '',
    admission_date: props.student?.admission_date ?? today,
    status: props.student?.status ?? 'active',
    create_user: false,
});

const error = (key: string) => (form.errors as Record<string, string>)[key];

const submit = () => {
    if (props.student) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/students/${props.student.id}`,
            {
                forceFormData: true,
            },
        );
        return;
    }

    form.post('/students', { forceFormData: true });
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Students', href: '/students' }],
    },
});
</script>

<template>
    <Head :title="editing ? 'Edit student' : 'Add student'" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="editing ? 'Edit student' : 'Add student'"
            description="Complete the student profile, guardian details, addresses, and optional login account."
        />

        <form class="space-y-6" @submit.prevent="submit">
            <Card>
                <CardHeader><CardTitle>Student Info</CardTitle></CardHeader>
                <CardContent class="space-y-6">
                    <ProfileImageInput
                        v-model="form.photo"
                        :current-image-url="student?.image_url"
                        default-image-url="/images/default-student.png"
                        attribution-url="https://www.flaticon.com/free-icon/graduated_3135773"
                        attribution-label="Student icon by Freepik - Flaticon"
                        label="Student photo"
                    />
                    <InputError :message="form.errors.photo" />

                    <div class="grid gap-5 md:grid-cols-3">
                        <div class="grid gap-2">
                            <Label for="admission_no">Admission no.</Label>
                            <Input
                                id="admission_no"
                                v-model="form.admission_no"
                                :placeholder="
                                    editing ? '' : 'Auto generated if blank'
                                "
                            />
                            <InputError :message="form.errors.admission_no" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="roll_no">Roll no.</Label>
                            <Input id="roll_no" v-model="form.roll_no" />
                            <InputError :message="form.errors.roll_no" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="class_id">Class</Label>
                            <select
                                id="class_id"
                                v-model="form.class_id"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                required
                            >
                                <option value="" disabled>Select class</option>
                                <option
                                    v-for="item in classes"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.class_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="section_id">Section</Label>
                            <select
                                id="section_id"
                                v-model="form.section_id"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                required
                            >
                                <option value="" disabled>
                                    Select section
                                </option>
                                <option
                                    v-for="item in sections"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.section_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="academic_session_id"
                                >Academic session</Label
                            >
                            <select
                                id="academic_session_id"
                                v-model="form.academic_session_id"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                required
                            >
                                <option value="" disabled>
                                    Select session
                                </option>
                                <option
                                    v-for="item in academicSessions"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.academic_session_id"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="dob">Date of birth</Label>
                            <Input
                                id="dob"
                                v-model="form.dob"
                                type="date"
                                required
                            />
                            <InputError :message="form.errors.dob" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="gender">Gender</Label>
                            <select
                                id="gender"
                                v-model="form.gender"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                required
                            >
                                <option value="" disabled>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            <InputError :message="form.errors.gender" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="student_category_id">Category</Label>
                            <select
                                id="student_category_id"
                                v-model="form.student_category_id"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            >
                                <option value="">No category</option>
                                <option
                                    v-for="item in categories"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.student_category_id"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="blood_group">Blood group</Label>
                            <Input
                                id="blood_group"
                                v-model="form.blood_group"
                                placeholder="O+"
                            />
                            <InputError :message="form.errors.blood_group" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="religion">Religion</Label>
                            <Input id="religion" v-model="form.religion" />
                            <InputError :message="form.errors.religion" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="status">Status</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                required
                            >
                                <option
                                    v-for="status in statuses"
                                    :key="status"
                                    :value="status"
                                >
                                    {{ status }}
                                </option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="admission_date">Admission date</Label>
                            <Input
                                id="admission_date"
                                v-model="form.admission_date"
                                type="date"
                                required
                            />
                            <InputError :message="form.errors.admission_date" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Guardian Info</CardTitle></CardHeader>
                <CardContent class="space-y-5">
                    <div class="flex flex-wrap gap-4 text-sm">
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.guardian_mode"
                                type="radio"
                                value="existing"
                            />
                            Select existing guardian
                        </label>
                        <label class="flex items-center gap-2">
                            <input
                                v-model="form.guardian_mode"
                                type="radio"
                                value="new"
                            />
                            Create new guardian
                        </label>
                    </div>

                    <div
                        v-if="form.guardian_mode === 'existing'"
                        class="grid gap-2"
                    >
                        <Label for="guardian_id">Guardian</Label>
                        <select
                            id="guardian_id"
                            v-model="form.guardian_id"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            required
                        >
                            <option value="" disabled>Select guardian</option>
                            <option
                                v-for="guardian in guardians"
                                :key="guardian.id"
                                :value="guardian.id"
                            >
                                {{ guardian.name }} - {{ guardian.phone }}
                            </option>
                        </select>
                        <InputError :message="form.errors.guardian_id" />
                    </div>

                    <div v-else class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label>Guardian name</Label>
                            <Input v-model="form.guardian.name" />
                            <InputError :message="error('guardian.name')" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Phone</Label>
                            <Input v-model="form.guardian.phone" />
                            <InputError :message="error('guardian.phone')" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Email</Label>
                            <Input v-model="form.guardian.email" type="email" />
                            <InputError :message="error('guardian.email')" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Relation</Label>
                            <select
                                v-model="form.guardian.relation"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            >
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Guardian</option>
                            </select>
                            <InputError :message="error('guardian.relation')" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Occupation</Label>
                            <Input v-model="form.guardian.occupation" />
                            <InputError
                                :message="error('guardian.occupation')"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label>Address</Label>
                            <Input v-model="form.guardian.address" />
                            <InputError :message="error('guardian.address')" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Address</CardTitle></CardHeader>
                <CardContent class="grid gap-5 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="present_address">Present address</Label>
                        <textarea
                            id="present_address"
                            v-model="form.present_address"
                            rows="4"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            required
                        />
                        <InputError :message="form.errors.present_address" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="permanent_address">Permanent address</Label>
                        <textarea
                            id="permanent_address"
                            v-model="form.permanent_address"
                            rows="4"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                        />
                        <InputError :message="form.errors.permanent_address" />
                    </div>
                    <label
                        class="flex items-center gap-2 text-sm md:col-span-2"
                    >
                        <input v-model="form.create_user" type="checkbox" />
                        Create linked student login account
                    </label>
                </CardContent>
            </Card>

            <div class="flex gap-3">
                <Button type="submit" :disabled="form.processing">
                    {{ editing ? 'Update student' : 'Add student' }}
                </Button>
                <Button variant="outline" as-child>
                    <Link href="/students">Cancel</Link>
                </Button>
            </div>
        </form>
    </div>
</template>
