<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Printer } from '@lucide/vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
const props = defineProps<{
    selectedStaff: any | null;
    staff: any[];
    days: string[];
    periods: any[];
    entries: any[];
}>();
const entry = (day: string, periodId: number) =>
    props.entries.find(
        (e) => e.day === day && e.timetable_period_id === periodId,
    );
const printPage = () => window.print();
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Teacher Schedule', href: '/timetable/teacher' },
        ],
    },
});
</script>
<template>
    <Head title="Teacher Schedule" />
    <div class="space-y-6 p-4 md:p-6">
        <div class="no-print">
            <ResourcePageHeader
                title="Teacher Schedule"
                description="View and print a teacher's weekly schedule."
            />
        </div>
        <Card class="no-print"
            ><CardContent class="flex gap-3 pt-6"
                ><select
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    @change="
                        router.get(
                            `/timetable/teacher/${($event.target as HTMLSelectElement).value}`,
                        )
                    "
                >
                    <option value="">Select teacher</option>
                    <option
                        v-for="s in staff"
                        :key="s.id"
                        :value="s.id"
                        :selected="selectedStaff?.id === s.id"
                    >
                        {{ s.name }}
                    </option></select
                ><Button @click="printPage"
                    ><Printer class="size-4" />Print</Button
                ></CardContent
            ></Card
        ><Card
            ><CardContent class="overflow-x-auto pt-6"
                ><h2 class="mb-4 text-xl font-semibold">
                    {{ selectedStaff?.name }}
                </h2>
                <table class="w-full min-w-220 text-sm">
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
                                class="border p-3"
                            >
                                <template v-if="entry(day, p.id)"
                                    ><b>{{ entry(day, p.id).subject.name }}</b>
                                    <div class="text-xs">
                                        {{ entry(day, p.id).class_room.name }} -
                                        {{ entry(day, p.id).section.name }}
                                    </div></template
                                ><span v-else class="text-muted-foreground"
                                    >Free</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
    </div>
</template>
<style>
@media print {
    .no-print,
    [data-sidebar='sidebar'],
    header {
        display: none !important;
    }
}
</style>
