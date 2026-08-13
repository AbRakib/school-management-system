<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
const props = defineProps<{ exam: any; studentCount: number }>();
const complete = (s: any) =>
    s.results.length >= props.studentCount && props.studentCount > 0;
const allComplete = () =>
    props.exam.subjects.length > 0 && props.exam.subjects.every(complete);
defineOptions({
    layout: { breadcrumbs: [{ title: 'Exams', href: '/exams' }] },
});
</script>
<template>
    <Head :title="exam.title" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="exam.title"
            :description="`${exam.class_room.name} · ${exam.academic_session.name}`"
        />
        <div class="flex flex-wrap gap-2">
            <Button variant="outline" as-child
                ><Link :href="`/exams/${exam.id}/subjects`"
                    >Manage Subjects</Link
                ></Button
            ><Button
                :disabled="!allComplete()"
                @click="router.post(`/exams/${exam.id}/publish`)"
                >Publish Results</Button
            >
        </div>
        <Card
            ><CardHeader><CardTitle>Subjects</CardTitle></CardHeader
            ><CardContent class="overflow-x-auto"
                ><table class="w-full min-w-160 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="py-3">Subject</th>
                            <th>Marks</th>
                            <th>Date</th>
                            <th>Progress</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="subject in exam.subjects"
                            :key="subject.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3 font-medium">
                                {{ subject.subject.name }}
                            </td>
                            <td>
                                {{ subject.full_marks }}/{{
                                    subject.pass_marks
                                }}
                            </td>
                            <td>{{ subject.exam_date || 'N/A' }}</td>
                            <td>
                                <Badge variant="secondary"
                                    >{{ subject.results.length }}/{{
                                        studentCount
                                    }}
                                    marked</Badge
                                >
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    <Button size="sm" as-child
                                        ><Link
                                            :href="`/exams/${exam.id}/marks-entry/${subject.id}`"
                                            >Enter Marks</Link
                                        ></Button
                                    >
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
    </div>
</template>
