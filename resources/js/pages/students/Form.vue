<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import ProfileImageInput from '@/components/ProfileImageInput.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Student = {
    id: number;
    admission_number: string;
    name: string;
    date_of_birth: string | null;
    gender: string | null;
    school_class_id: number;
    guardian_id: number;
    phone: string | null;
    admission_date: string;
    address: string | null;
    image_url: string;
};

type Option = { id: number; name: string; phone?: string };

const props = defineProps<{
    student?: Student;
    classes: Option[];
    guardians: Option[];
}>();
const editing = Boolean(props.student);

const today = new Date().toISOString().slice(0, 10);
const form = useForm({
    admission_number: props.student?.admission_number ?? '',
    name: props.student?.name ?? '',
    date_of_birth: props.student?.date_of_birth ?? '',
    gender: props.student?.gender ?? '',
    school_class_id: props.student?.school_class_id ?? '',
    guardian_id: props.student?.guardian_id ?? '',
    phone: props.student?.phone ?? '',
    admission_date: props.student?.admission_date ?? today,
    address: props.student?.address ?? '',
    image: null as File | null,
});

const submit = () => {
    if (props.student) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/students/${props.student.id}`,
            { forceFormData: true },
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
    <Head :title="editing ? 'Edit student' : 'Admit student'" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="editing ? 'Edit student' : 'Admit student'"
            description="Enter student details and assign a class and parent."
        />

        <Card class="max-w-4xl">
            <CardContent class="pt-6">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <ProfileImageInput
                            v-model="form.image"
                            :current-image-url="student?.image_url"
                            default-image-url="/images/default-student.png"
                            attribution-url="https://www.flaticon.com/free-icon/graduated_3135773"
                            attribution-label="Student icon by Freepik - Flaticon"
                            label="Student image"
                        />
                        <InputError :message="form.errors.image" />
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="admission_number">
                                Admission number
                            </Label>
                            <Input
                                id="admission_number"
                                v-model="form.admission_number"
                                placeholder="SMS-0001"
                                required
                            />
                            <InputError
                                :message="form.errors.admission_number"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="name">Student name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="school_class_id">Class</Label>
                            <select
                                id="school_class_id"
                                v-model="form.school_class_id"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                required
                            >
                                <option value="" disabled>Select class</option>
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
                            <Label for="guardian_id">Parent / guardian</Label>
                            <select
                                id="guardian_id"
                                v-model="form.guardian_id"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                required
                            >
                                <option value="" disabled>Select parent</option>
                                <option
                                    v-for="guardian in guardians"
                                    :key="guardian.id"
                                    :value="guardian.id"
                                >
                                    {{ guardian.name }} — {{ guardian.phone }}
                                </option>
                            </select>
                            <InputError :message="form.errors.guardian_id" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="date_of_birth">Date of birth</Label>
                            <Input
                                id="date_of_birth"
                                v-model="form.date_of_birth"
                                type="date"
                            />
                            <InputError :message="form.errors.date_of_birth" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="gender">Gender</Label>
                            <select
                                id="gender"
                                v-model="form.gender"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            >
                                <option value="">Not specified</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                            <InputError :message="form.errors.gender" />
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
                        <div class="grid gap-2">
                            <Label for="phone">Student phone</Label>
                            <Input id="phone" v-model="form.phone" />
                            <InputError :message="form.errors.phone" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="address">Address</Label>
                        <textarea
                            id="address"
                            v-model="form.address"
                            rows="3"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                        />
                        <InputError :message="form.errors.address" />
                    </div>
                    <div class="flex gap-3">
                        <Button type="submit" :disabled="form.processing">
                            {{ editing ? 'Update student' : 'Admit student' }}
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/students">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
