<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Student = {
    id: number;
    admission_no: string;
    name: string;
    dob: string | null;
    gender: string;
    blood_group: string | null;
    religion: string | null;
    present_address: string | null;
    permanent_address: string | null;
    admission_date: string;
    status: string;
    image_url: string;
    full_class_name: string;
    user: { id: number; email: string } | null;
    guardian: {
        name: string;
        phone: string;
        email: string | null;
        relation: string | null;
        occupation: string | null;
        address: string | null;
    };
    academic_session: { id: number; name: string } | null;
    student_category: { id: number; name: string } | null;
};

defineProps<{ student: Student }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Students', href: '/students' }],
    },
});
</script>

<template>
    <Head :title="student.name" />

    <div class="space-y-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    {{ student.name }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ student.admission_no }} · {{ student.full_class_name }}
                </p>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" as-child
                    ><Link href="/students">Back</Link></Button
                >
                <Button as-child
                    ><Link :href="`/students/${student.id}/edit`"
                        >Edit</Link
                    ></Button
                >
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <Card class="xl:col-span-2">
                <CardHeader><CardTitle>Basic info</CardTitle></CardHeader>
                <CardContent class="grid gap-6 md:grid-cols-[160px_1fr]">
                    <img
                        :src="student.image_url"
                        :alt="student.name"
                        class="size-36 rounded-lg border object-cover"
                    />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs text-muted-foreground">Status</p>
                            <Badge variant="secondary">{{
                                student.status
                            }}</Badge>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Session</p>
                            <p class="font-medium">
                                {{ student.academic_session?.name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Date of birth
                            </p>
                            <p class="font-medium">{{ student.dob }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Gender</p>
                            <p class="font-medium">{{ student.gender }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Blood group
                            </p>
                            <p class="font-medium">
                                {{ student.blood_group || 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Category
                            </p>
                            <p class="font-medium">
                                {{ student.student_category?.name || 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Religion
                            </p>
                            <p class="font-medium">
                                {{ student.religion || 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Admission date
                            </p>
                            <p class="font-medium">
                                {{ student.admission_date }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Login account
                            </p>
                            <p class="font-medium">
                                {{ student.user?.email || 'Not created' }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Guardian</CardTitle></CardHeader>
                <CardContent class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-muted-foreground">Name</p>
                        <p class="font-medium">{{ student.guardian.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Relation</p>
                        <p class="font-medium">
                            {{ student.guardian.relation }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Phone</p>
                        <p class="font-medium">{{ student.guardian.phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Email</p>
                        <p class="font-medium">
                            {{ student.guardian.email || 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Occupation</p>
                        <p class="font-medium">
                            {{ student.guardian.occupation || 'N/A' }}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <Card>
                <CardHeader><CardTitle>Addresses</CardTitle></CardHeader>
                <CardContent class="grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs text-muted-foreground">Present</p>
                        <p class="mt-1 text-sm">
                            {{ student.present_address }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Permanent</p>
                        <p class="mt-1 text-sm">
                            {{
                                student.permanent_address ||
                                student.present_address
                            }}
                        </p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader
                    ><CardTitle>Attendance summary</CardTitle></CardHeader
                >
                <CardContent
                    ><p class="text-sm text-muted-foreground">
                        Attendance module placeholder. Summary will appear here
                        when connected.
                    </p></CardContent
                >
            </Card>
            <Card>
                <CardHeader><CardTitle>Results summary</CardTitle></CardHeader>
                <CardContent
                    ><p class="text-sm text-muted-foreground">
                        Exam and result module placeholder. Latest marks and
                        report cards will appear here.
                    </p></CardContent
                >
            </Card>
            <Card>
                <CardHeader
                    ><CardTitle>Fee status summary</CardTitle></CardHeader
                >
                <CardContent
                    ><p class="text-sm text-muted-foreground">
                        Fees module placeholder. Dues, paid amount, and
                        collection actions will appear here.
                    </p></CardContent
                >
            </Card>
        </div>
    </div>
</template>
