<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
defineProps<{
    invoices: any[];
    filters: Record<string, string | null>;
    options: { classes: any[]; sections: any[] };
}>();
defineOptions({
    layout: { breadcrumbs: [{ title: 'Due Fees', href: '/fees/due' }] },
});
</script>
<template>
    <Head title="Due Fees" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Due/Pending Fees"
            description="All outstanding invoices sorted by due amount."
        /><Card
            ><CardContent class="overflow-x-auto pt-6"
                ><table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Student</th>
                            <th>Class</th>
                            <th>Month</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="i in invoices" :key="i.id" class="border-b">
                            <td class="py-3">{{ i.student.name }}</td>
                            <td>
                                {{ i.student.school_class?.name }} -
                                {{ i.student.section?.name }}
                            </td>
                            <td>{{ i.month }}</td>
                            <td class="font-medium">{{ i.due_amount }}</td>
                            <td>{{ i.status }}</td>
                            <td class="text-right">
                                <Button size="sm" as-child
                                    ><Link
                                        :href="`/fees/collect?student_id=${i.student_id}`"
                                        >Collect</Link
                                    ></Button
                                >
                            </td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
    </div>
</template>
