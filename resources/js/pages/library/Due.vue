<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Card, CardContent } from '@/components/ui/card';
defineProps<{ issues: any[]; fineRate: number }>();
const days = (due: string) =>
    Math.max(Math.ceil((Date.now() - new Date(due).getTime()) / 86400000), 0);
defineOptions({
    layout: { breadcrumbs: [{ title: 'Due Books', href: '/library/due' }] },
});
</script>
<template>
    <Head title="Due Books" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Due Books"
            description="Currently issued and overdue books."
        /><Card
            ><CardContent class="overflow-x-auto pt-6"
                ><table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Book</th>
                            <th>Borrower</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                            <th>Fine</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="i in issues" :key="i.id" class="border-b">
                            <td class="py-3 font-medium">{{ i.book.title }}</td>
                            <td>{{ i.borrower.name }}</td>
                            <td>{{ i.due_date }}</td>
                            <td>{{ days(i.due_date) }}</td>
                            <td>{{ days(i.due_date) * fineRate }}</td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
    </div>
</template>
