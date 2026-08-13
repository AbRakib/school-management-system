<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
type Option = {
    id: number;
    name: string;
    code?: string;
    school_class_id?: number;
    staff_id?: string;
};
type Period = {
    id: number;
    name: string;
    start_time: string;
    end_time: string;
};
const props = defineProps<{
    days: string[];
    periods: Period[];
    entries: any[];
    assignments: any[];
    busySlots: any[];
    filters: Record<string, string | null>;
    options: {
        classes: Option[];
        sections: Option[];
        sessions: Option[];
        subjects: Option[];
    };
}>();
const filters = reactive({
    class_id: props.filters.class_id ?? '',
    section_id: props.filters.section_id ?? '',
    academic_session_id: props.filters.academic_session_id ?? '',
});
const selected = ref<{ day: string; period: Period } | null>(null);
const form = useForm({
    class_id: '',
    section_id: '',
    academic_session_id: '',
    day: '',
    timetable_period_id: '',
    subject_id: '',
    staff_id: '',
});
const entry = (day: string, periodId: number) =>
    props.entries.find(
        (e) => e.day === day && e.timetable_period_id === periodId,
    );
const teachers = computed(() =>
    props.assignments
        .filter((a) => String(a.subject_id) === String(form.subject_id))
        .map((a) => a.staff),
);
const conflict = computed(
    () =>
        form.staff_id &&
        props.busySlots.some(
            (s) =>
                String(s.staff_id) === String(form.staff_id) &&
                s.day === form.day &&
                String(s.timetable_period_id) ===
                    String(form.timetable_period_id) &&
                s.id !== entry(form.day, Number(form.timetable_period_id))?.id,
        ),
);
const load = () =>
    router.get('/timetable/builder', filters, {
        preserveState: true,
        replace: true,
    });
const open = (day: string, period: Period) => {
    const e = entry(day, period.id);
    selected.value = { day, period };
    form.class_id = filters.class_id;
    form.section_id = filters.section_id;
    form.academic_session_id = filters.academic_session_id;
    form.day = day;
    form.timetable_period_id = String(period.id);
    form.subject_id = e?.subject_id ?? '';
    form.staff_id = e?.staff_id ?? '';
};
const save = () =>
    form.post('/timetable/builder', {
        preserveScroll: true,
        onSuccess: () => {
            selected.value = null;
            load();
        },
    });
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Timetable Builder', href: '/timetable/builder' },
        ],
    },
});
</script>
<template>
    <Head title="Timetable Builder" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Class Routine"
            description="Build class routines by day and period."
        /><Card
            ><CardContent class="grid gap-3 pt-6 md:grid-cols-3"
                ><select
                    v-model="filters.class_id"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    @change="load"
                >
                    <option value="">Class</option>
                    <option
                        v-for="c in options.classes"
                        :key="c.id"
                        :value="c.id"
                    >
                        {{ c.name }}
                    </option></select
                ><select
                    v-model="filters.section_id"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    @change="load"
                >
                    <option value="">Section</option>
                    <option
                        v-for="s in options.sections"
                        :key="s.id"
                        :value="s.id"
                    >
                        {{ s.name }}
                    </option></select
                ><select
                    v-model="filters.academic_session_id"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    @change="load"
                >
                    <option value="">Session</option>
                    <option
                        v-for="s in options.sessions"
                        :key="s.id"
                        :value="s.id"
                    >
                        {{ s.name }}
                    </option>
                </select></CardContent
            ></Card
        ><Card
            ><CardContent class="overflow-x-auto pt-6"
                ><table class="w-full min-w-220 text-sm">
                    <thead>
                        <tr>
                            <th class="border p-3 text-left">Period</th>
                            <th
                                v-for="day in days"
                                :key="day"
                                class="border p-3 capitalize"
                            >
                                {{ day }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in periods" :key="p.id">
                            <td class="border p-3 font-medium">
                                {{ p.name }}
                                <div class="text-xs text-muted-foreground">
                                    {{ p.start_time }}-{{ p.end_time }}
                                </div>
                            </td>
                            <td
                                v-for="day in days"
                                :key="day"
                                class="border p-2"
                            >
                                <button
                                    class="min-h-20 w-full rounded-md bg-muted p-2 text-left hover:bg-muted/80"
                                    @click="open(day, p)"
                                >
                                    <template v-if="entry(day, p.id)"
                                        ><b>{{
                                            entry(day, p.id).subject.name
                                        }}</b>
                                        <div class="text-xs">
                                            {{ entry(day, p.id).staff.name }}
                                        </div></template
                                    ><span v-else class="text-muted-foreground"
                                        >Assign</span
                                    >
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
        <div
            v-if="selected"
            class="fixed inset-0 z-50 grid place-items-center bg-black/40 p-4"
        >
            <Card class="w-full max-w-lg"
                ><CardContent class="space-y-4 pt-6"
                    ><h2 class="font-semibold capitalize">
                        {{ selected.day }} · {{ selected.period.name }}
                    </h2>
                    <select
                        v-model="form.subject_id"
                        class="h-9 w-full rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="">Subject</option>
                        <option
                            v-for="a in assignments"
                            :key="a.id"
                            :value="a.subject_id"
                        >
                            {{ a.subject.name }} ({{ a.subject.code }})
                        </option></select
                    ><select
                        v-model="form.staff_id"
                        class="h-9 w-full rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="">Teacher</option>
                        <option v-for="t in teachers" :key="t.id" :value="t.id">
                            {{ t.name }} ({{ t.staff_id }})
                        </option>
                    </select>
                    <p
                        v-if="conflict"
                        class="rounded-md bg-rose-50 p-2 text-sm text-rose-600"
                    >
                        This teacher is already booked for this day and period.
                    </p>
                    <div class="flex justify-end gap-2">
                        <Button variant="outline" @click="selected = null"
                            >Cancel</Button
                        ><Button :disabled="!!conflict" @click="save"
                            >Save</Button
                        >
                    </div></CardContent
                ></Card
            >
        </div>
    </div>
</template>
