<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import ProfileImageInput from '@/components/ProfileImageInput.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Teacher = {
    id: number;
    name: string;
    email: string | null;
    phone: string | null;
    designation: 'teacher' | 'head_teacher';
    subject: string | null;
    joining_date: string | null;
    address: string | null;
    image_url: string;
};

const props = defineProps<{ teacher?: Teacher }>();
const editing = Boolean(props.teacher);

const form = useForm({
    name: props.teacher?.name ?? '',
    email: props.teacher?.email ?? '',
    phone: props.teacher?.phone ?? '',
    designation: props.teacher?.designation ?? 'teacher',
    subject: props.teacher?.subject ?? '',
    joining_date: props.teacher?.joining_date ?? '',
    address: props.teacher?.address ?? '',
    image: null as File | null,
});

const submit = () => {
    if (props.teacher) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/teachers/${props.teacher.id}`,
            { forceFormData: true },
        );

        return;
    }

    form.post('/teachers', { forceFormData: true });
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Teachers', href: '/teachers' }],
    },
});
</script>

<template>
    <Head :title="editing ? 'Edit teacher' : 'Add teacher'" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="editing ? 'Edit teacher' : 'Add teacher'"
            description="Enter the teacher's personal and professional details."
        />

        <Card class="max-w-3xl">
            <CardContent class="pt-6">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <ProfileImageInput
                            v-model="form.image"
                            :current-image-url="teacher?.image_url"
                            default-image-url="/images/default-teacher.png"
                            attribution-url="https://www.flaticon.com/free-icon/teacher_3307318"
                            attribution-label="Teacher icon by Freepik - Flaticon"
                            label="Teacher image"
                        />
                        <InputError :message="form.errors.image" />
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Full name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="designation">Designation</Label>
                            <select
                                id="designation"
                                v-model="form.designation"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                                required
                            >
                                <option value="teacher">Teacher</option>
                                <option value="head_teacher">
                                    Head Teacher
                                </option>
                            </select>
                            <InputError :message="form.errors.designation" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="subject">Subject</Label>
                            <Input id="subject" v-model="form.subject" />
                            <InputError :message="form.errors.subject" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="joining_date">Joining date</Label>
                            <Input
                                id="joining_date"
                                v-model="form.joining_date"
                                type="date"
                            />
                            <InputError :message="form.errors.joining_date" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                            />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="phone">Phone</Label>
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
                            {{ editing ? 'Update teacher' : 'Save teacher' }}
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/teachers">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
