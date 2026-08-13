<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2 } from '@lucide/vue';
import { watch } from 'vue';
import InputError from '@/components/InputError.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type StaffRow = {
    id: number;
    staff_id: string;
    name: string;
    photo_url: string;
    department: string | null;
    designation: string | null;
    status: string;
    check_in: string;
    check_out: string;
};

const props = defineProps<{
    staff: StaffRow[];
    date: string;
    statuses: string[];
}>();
const form = useForm({
    date: props.date,
    attendances: props.staff.map((staff) => ({
        staff_id: staff.id,
        status: staff.status,
        check_in: staff.check_in ?? '',
        check_out: staff.check_out ?? '',
    })),
});

watch(
    () => props.staff,
    (staff) => {
        form.attendances = staff.map((item) => ({
            staff_id: item.id,
            status: item.status,
            check_in: item.check_in ?? '',
            check_out: item.check_out ?? '',
        }));
    },
);

const loadStaff = () =>
    router.get(
        '/attendance/staff',
        { date: form.date },
        { preserveState: true, replace: true },
    );
const markAllPresent = () =>
    form.attendances.forEach((attendance) => (attendance.status = 'present'));
const save = () => form.post('/attendance/staff');
const statusClass = (active: boolean) =>
    active
        ? 'bg-primary text-primary-foreground'
        : 'bg-muted text-muted-foreground hover:bg-muted/80';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Staff Attendance', href: '/attendance/staff' }],
    },
});
</script>

<template>
    <Head title="Staff Attendance" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Staff Attendance"
            description="Mark all staff attendance for a date with a single save."
        />

        <Card>
            <CardContent
                class="flex flex-col gap-3 pt-6 md:flex-row md:items-center"
            >
                <Input
                    v-model="form.date"
                    type="date"
                    class="md:max-w-56"
                    @change="loadStaff"
                />
                <Button variant="outline" type="button" @click="markAllPresent">
                    <CheckCircle2 class="size-4" />
                    Mark All Present
                </Button>
                <Button
                    class="md:ml-auto"
                    :disabled="form.processing"
                    @click="save"
                    >Save Attendance</Button
                >
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Staff list</CardTitle></CardHeader>
            <CardContent class="space-y-3">
                <InputError :message="form.errors.attendances" />
                <div
                    v-for="(staffMember, index) in staff"
                    :key="staffMember.id"
                    class="grid gap-3 rounded-lg border p-3 xl:grid-cols-[1fr_auto_120px_120px] xl:items-center"
                >
                    <div class="flex items-center gap-3">
                        <Avatar class="size-11 border"
                            ><AvatarImage
                                :src="staffMember.photo_url"
                                :alt="staffMember.name"
                                class="object-cover"
                        /></Avatar>
                        <div>
                            <p class="font-medium">{{ staffMember.name }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ staffMember.staff_id }} ·
                                {{ staffMember.designation }}
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
                                ),
                            ]"
                            @click="form.attendances[index].status = status"
                        >
                            {{ status.replace('_', ' ') }}
                        </button>
                    </div>
                    <Input
                        v-model="form.attendances[index].check_in"
                        type="time"
                    />
                    <Input
                        v-model="form.attendances[index].check_out"
                        type="time"
                    />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
