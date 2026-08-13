<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import ProfileImageInput from '@/components/ProfileImageInput.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Option = { id: number; name: string; is_teaching_staff?: boolean };
type Staff = {
    id: number;
    department_id: number;
    designation_id: number;
    name: string;
    phone: string;
    email: string;
    dob: string;
    gender: string;
    joining_date: string;
    qualification: string | null;
    address: string;
    photo_url: string;
    salary: string | number;
    status: string;
};

const props = defineProps<{
    staffMember?: Staff | null;
    departments: Option[];
    designations: Option[];
    statuses: string[];
}>();

const editing = Boolean(props.staffMember);
const form = useForm({
    department_id: props.staffMember?.department_id ?? '',
    designation_id: props.staffMember?.designation_id ?? '',
    name: props.staffMember?.name ?? '',
    phone: props.staffMember?.phone ?? '',
    email: props.staffMember?.email ?? '',
    dob: props.staffMember?.dob ?? '',
    gender: props.staffMember?.gender ?? '',
    joining_date: props.staffMember?.joining_date ?? '',
    qualification: props.staffMember?.qualification ?? '',
    address: props.staffMember?.address ?? '',
    photo: null as File | null,
    salary: props.staffMember?.salary ?? 0,
    status: props.staffMember?.status ?? 'active',
});

const submit = () => {
    if (props.staffMember) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/staff/${props.staffMember.id}`,
            { forceFormData: true },
        );
        return;
    }

    form.post('/staff', { forceFormData: true });
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Staff', href: '/staff' }],
    },
});
</script>

<template>
    <Head :title="editing ? 'Edit staff' : 'Add staff'" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="editing ? 'Edit staff' : 'Add staff'"
            description="Enter personal, job, salary, and contact information."
        />

        <form class="space-y-6" @submit.prevent="submit">
            <Card>
                <CardHeader><CardTitle>Personal info</CardTitle></CardHeader>
                <CardContent class="space-y-6">
                    <ProfileImageInput
                        v-model="form.photo"
                        :current-image-url="staffMember?.photo_url"
                        default-image-url="/images/default-teacher.png"
                        attribution-url="https://www.flaticon.com/free-icon/teacher_3307318"
                        attribution-label="Teacher icon by Freepik - Flaticon"
                        label="Staff photo"
                    />
                    <InputError :message="form.errors.photo" />
                    <div class="grid gap-5 md:grid-cols-3">
                        <div class="grid gap-2">
                            <Label>Name</Label
                            ><Input v-model="form.name" required /><InputError
                                :message="form.errors.name"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label>Email</Label
                            ><Input
                                v-model="form.email"
                                type="email"
                                required
                            /><InputError :message="form.errors.email" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Phone</Label
                            ><Input v-model="form.phone" required /><InputError
                                :message="form.errors.phone"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label>Date of birth</Label
                            ><Input
                                v-model="form.dob"
                                type="date"
                                required
                            /><InputError :message="form.errors.dob" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Gender</Label>
                            <select
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
                            <Label>Joining date</Label
                            ><Input
                                v-model="form.joining_date"
                                type="date"
                                required
                            /><InputError :message="form.errors.joining_date" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Job details</CardTitle></CardHeader>
                <CardContent class="grid gap-5 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label>Department</Label>
                        <select
                            v-model="form.department_id"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            required
                        >
                            <option value="" disabled>Select department</option>
                            <option
                                v-for="department in departments"
                                :key="department.id"
                                :value="department.id"
                            >
                                {{ department.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.department_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label>Designation</Label>
                        <select
                            v-model="form.designation_id"
                            class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            required
                        >
                            <option value="" disabled>
                                Select designation
                            </option>
                            <option
                                v-for="designation in designations"
                                :key="designation.id"
                                :value="designation.id"
                            >
                                {{ designation.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.designation_id" />
                    </div>
                    <div class="grid gap-2">
                        <Label>Salary</Label
                        ><Input
                            v-model="form.salary"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                        /><InputError :message="form.errors.salary" />
                    </div>
                    <div class="grid gap-2">
                        <Label>Status</Label>
                        <select
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
                    <div class="grid gap-2 md:col-span-2">
                        <Label>Qualification</Label>
                        <textarea
                            v-model="form.qualification"
                            rows="3"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                        />
                        <InputError :message="form.errors.qualification" />
                    </div>
                    <div class="grid gap-2 md:col-span-2">
                        <Label>Address</Label>
                        <textarea
                            v-model="form.address"
                            rows="3"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            required
                        />
                        <InputError :message="form.errors.address" />
                    </div>
                </CardContent>
            </Card>

            <div class="flex gap-3">
                <Button type="submit" :disabled="form.processing">{{
                    editing ? 'Update staff' : 'Create staff'
                }}</Button>
                <Button variant="outline" as-child
                    ><Link href="/staff">Cancel</Link></Button
                >
            </div>
        </form>
    </div>
</template>
