<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Stop = {
    id: number;
    name: string;
    pickup_time: string | null;
    order: number;
};
type Route = {
    id: number;
    name: string;
    fare_amount: string | number;
    stops: Stop[];
    vehicle: { id: number; vehicle_number: string; type: string };
};
type Student = {
    id: number;
    name: string;
    admission_no: string | null;
    school_class?: { name: string } | null;
    section?: { name: string } | null;
};
type Assignment = {
    id: number;
    student: Student;
    route: Route;
    stop: Stop;
};

const props = defineProps<{
    routes: Route[];
    students: Student[];
    assignments: Assignment[];
    academicSession: { id: number; name: string } | null;
    filters: { routeId: string | null; studentSearch: string | null };
}>();

const filterForm = useForm({
    route_id: props.filters.routeId ?? '',
    student_search: props.filters.studentSearch ?? '',
});
const form = useForm({
    student_id: '',
    transport_route_id: props.filters.routeId ?? '',
    transport_stop_id: '',
    academic_session_id: props.academicSession?.id ?? '',
});

const selectedRoute = computed(() =>
    props.routes.find(
        (route) => String(route.id) === String(form.transport_route_id),
    ),
);
const selectedFilterRoute = computed(() =>
    props.routes.find(
        (route) => String(route.id) === String(filterForm.route_id),
    ),
);

const load = () => {
    form.transport_route_id = filterForm.route_id;
    form.transport_stop_id = '';
    router.get(
        '/student-transports',
        {
            route_id: filterForm.route_id,
            student_search: filterForm.student_search,
        },
        { preserveState: true, replace: true },
    );
};

const pickStudent = (student: Student) => {
    form.student_id = String(student.id);
};

const assign = () => {
    form.post('/student-transports', {
        onSuccess: () => {
            form.student_id = '';
            form.transport_stop_id = '';
        },
    });
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Student Transport', href: '/student-transports' },
        ],
    },
});
</script>

<template>
    <Head title="Student Transport Assignment" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Student Assignment"
            description="Assign active students to a transport route and pickup stop."
        />

        <Card>
            <CardContent class="grid gap-3 pt-6 md:grid-cols-3">
                <select
                    v-model="filterForm.route_id"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    @change="load"
                >
                    <option value="">All routes</option>
                    <option
                        v-for="route in routes"
                        :key="route.id"
                        :value="route.id"
                    >
                        {{ route.name }}
                    </option>
                </select>
                <Input
                    v-model="filterForm.student_search"
                    placeholder="Search student name or admission no"
                    @keyup.enter="load"
                />
                <Button type="button" @click="load">Search</Button>
            </CardContent>
        </Card>

        <div class="grid gap-6 xl:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle>Assign Student</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-2">
                        <button
                            v-for="student in students"
                            :key="student.id"
                            type="button"
                            class="rounded border p-3 text-left"
                            :class="{
                                'border-primary bg-primary/5':
                                    String(form.student_id) ===
                                    String(student.id),
                            }"
                            @click="pickStudent(student)"
                        >
                            <div class="font-medium">{{ student.name }}</div>
                            <div class="text-xs text-muted-foreground">
                                {{ student.admission_no }} ·
                                {{ student.school_class?.name || 'No class' }}
                                <span v-if="student.section"
                                    >- {{ student.section.name }}</span
                                >
                            </div>
                        </button>
                    </div>

                    <select
                        v-model="form.transport_route_id"
                        class="h-9 w-full rounded-md border bg-transparent px-3 text-sm"
                        @change="form.transport_stop_id = ''"
                    >
                        <option value="">Route</option>
                        <option
                            v-for="route in routes"
                            :key="route.id"
                            :value="route.id"
                        >
                            {{ route.name }}
                        </option>
                    </select>
                    <select
                        v-model="form.transport_stop_id"
                        class="h-9 w-full rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="">Pickup stop</option>
                        <option
                            v-for="stop in selectedRoute?.stops ?? []"
                            :key="stop.id"
                            :value="stop.id"
                        >
                            {{ stop.order }}. {{ stop.name }}
                            {{
                                stop.pickup_time ? `(${stop.pickup_time})` : ''
                            }}
                        </option>
                    </select>
                    <div
                        class="rounded border p-3 text-sm text-muted-foreground"
                    >
                        Session:
                        <span class="font-medium text-foreground">
                            {{ academicSession?.name || 'No active session' }}
                        </span>
                    </div>
                    <Button
                        class="w-full"
                        :disabled="!academicSession"
                        @click="assign"
                    >
                        Assign Transport
                    </Button>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>
                        Assigned Students
                        <span v-if="selectedFilterRoute"
                            >- {{ selectedFilterRoute.name }}</span
                        >
                    </CardTitle>
                </CardHeader>
                <CardContent class="overflow-x-auto">
                    <table class="w-full min-w-150 text-sm">
                        <thead class="border-b text-left">
                            <tr>
                                <th class="py-3">Student</th>
                                <th>Route</th>
                                <th>Stop</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="assignment in assignments"
                                :key="assignment.id"
                                class="border-b"
                            >
                                <td class="py-3">
                                    <div class="font-medium">
                                        {{ assignment.student.name }}
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ assignment.student.admission_no }}
                                    </div>
                                </td>
                                <td>{{ assignment.route.name }}</td>
                                <td>
                                    {{ assignment.stop.name }}
                                    <div class="text-xs text-muted-foreground">
                                        {{
                                            assignment.stop.pickup_time ||
                                            'No pickup time'
                                        }}
                                    </div>
                                </td>
                                <td class="text-right">
                                    <Button
                                        variant="ghost"
                                        @click="
                                            router.delete(
                                                `/student-transports/${assignment.id}`,
                                            )
                                        "
                                        >Unassign</Button
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
