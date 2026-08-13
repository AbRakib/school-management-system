<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Assignment = {
    id: number;
    class_room: { id: number; name: string };
    section: { id: number; name: string };
    subject: { id: number; name: string; code: string };
    academic_session: { id: number; name: string };
};
type Staff = {
    id: number;
    staff_id: string;
    name: string;
    phone: string;
    email: string;
    dob: string;
    gender: string;
    joining_date: string;
    qualification: string | null;
    address: string;
    photo_url: string;
    salary: string;
    status: string;
    user: { id: number; email: string };
    department: { id: number; name: string };
    designation: { id: number; name: string; is_teaching_staff: boolean };
    assignments: Assignment[];
};

defineProps<{ staffMember: Staff }>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Staff', href: '/staff' }],
    },
});
</script>

<template>
    <Head :title="staffMember.name" />

    <div class="space-y-6 p-4 md:p-6">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    {{ staffMember.name }}
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ staffMember.staff_id }} ·
                    {{ staffMember.designation.name }}
                </p>
            </div>
            <div class="flex gap-2">
                <Button variant="outline" as-child
                    ><Link href="/staff">Back</Link></Button
                >
                <Button variant="outline" as-child
                    ><Link :href="`/staff/${staffMember.id}/assignments`"
                        >Assignments</Link
                    ></Button
                >
                <Button as-child
                    ><Link :href="`/staff/${staffMember.id}/edit`"
                        >Edit</Link
                    ></Button
                >
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <Card class="xl:col-span-2">
                <CardHeader><CardTitle>Profile</CardTitle></CardHeader>
                <CardContent class="grid gap-6 md:grid-cols-[160px_1fr]">
                    <img
                        :src="staffMember.photo_url"
                        :alt="staffMember.name"
                        class="size-36 rounded-lg border object-cover"
                    />
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs text-muted-foreground">Status</p>
                            <Badge variant="secondary">{{
                                staffMember.status
                            }}</Badge>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Department
                            </p>
                            <p class="font-medium">
                                {{ staffMember.department.name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Email</p>
                            <p class="font-medium">{{ staffMember.email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Phone</p>
                            <p class="font-medium">{{ staffMember.phone }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Date of birth
                            </p>
                            <p class="font-medium">{{ staffMember.dob }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Gender</p>
                            <p class="font-medium">{{ staffMember.gender }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Joining date
                            </p>
                            <p class="font-medium">
                                {{ staffMember.joining_date }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Salary</p>
                            <p class="font-medium">{{ staffMember.salary }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader><CardTitle>Account</CardTitle></CardHeader>
                <CardContent class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-muted-foreground">Login email</p>
                        <p class="font-medium">{{ staffMember.user.email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">
                            Teaching staff
                        </p>
                        <p class="font-medium">
                            {{
                                staffMember.designation.is_teaching_staff
                                    ? 'Yes'
                                    : 'No'
                            }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">
                            Qualification
                        </p>
                        <p class="font-medium">
                            {{ staffMember.qualification || 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Address</p>
                        <p class="font-medium">{{ staffMember.address }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <Card>
            <CardHeader class="flex-row items-center justify-between space-y-0">
                <CardTitle>Assignments</CardTitle>
                <Button variant="outline" size="sm" as-child
                    ><Link :href="`/staff/${staffMember.id}/assignments`"
                        >Manage</Link
                    ></Button
                >
            </CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-160 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="py-3 font-medium">Class</th>
                            <th class="py-3 font-medium">Section</th>
                            <th class="py-3 font-medium">Subject</th>
                            <th class="py-3 font-medium">Session</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="assignment in staffMember.assignments"
                            :key="assignment.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3">
                                {{ assignment.class_room.name }}
                            </td>
                            <td class="py-3">{{ assignment.section.name }}</td>
                            <td class="py-3">{{ assignment.subject.name }}</td>
                            <td class="py-3">
                                {{ assignment.academic_session.name }}
                            </td>
                        </tr>
                        <tr v-if="staffMember.assignments.length === 0">
                            <td
                                colspan="4"
                                class="py-10 text-center text-muted-foreground"
                            >
                                No assignments yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
