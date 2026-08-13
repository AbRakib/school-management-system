<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Scale = {
    id: number;
    grade_name: string;
    min_percentage: string;
    max_percentage: string;
    grade_point: string;
    remarks: string | null;
};
defineProps<{ scales: Scale[] }>();
const editingId = ref<number | null>(null);
const form = useForm({
    grade_name: '',
    min_percentage: '',
    max_percentage: '',
    grade_point: '',
    remarks: '',
});
const save = () =>
    editingId.value
        ? form.put(`/grading-scales/${editingId.value}`, {
              onSuccess: () => {
                  editingId.value = null;
                  form.reset();
              },
          })
        : form.post('/grading-scales', { onSuccess: () => form.reset() });
const edit = (s: Scale) => {
    editingId.value = s.id;
    form.grade_name = s.grade_name;
    form.min_percentage = s.min_percentage;
    form.max_percentage = s.max_percentage;
    form.grade_point = s.grade_point;
    form.remarks = s.remarks ?? '';
};
defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Grading Scale', href: '/grading-scales' }],
    },
});
</script>
<template>
    <Head title="Grading Scale" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Grading System"
            description="Define grade ranges and grade points used by ResultService."
        />
        <Card
            ><CardContent class="space-y-4 pt-6">
                <form class="grid gap-3 md:grid-cols-6" @submit.prevent="save">
                    <Input
                        v-model="form.grade_name"
                        placeholder="A+"
                        required
                    /><Input
                        v-model="form.min_percentage"
                        type="number"
                        placeholder="Min"
                        required
                    /><Input
                        v-model="form.max_percentage"
                        type="number"
                        placeholder="Max"
                        required
                    /><Input
                        v-model="form.grade_point"
                        type="number"
                        step="0.01"
                        placeholder="Point"
                        required
                    /><Input
                        v-model="form.remarks"
                        placeholder="Remarks"
                    /><Button>{{ editingId ? 'Update' : 'Add' }}</Button>
                </form>
                <table class="w-full text-sm">
                    <tbody>
                        <tr
                            v-for="scale in scales"
                            :key="scale.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3 font-medium">
                                {{ scale.grade_name }}
                            </td>
                            <td>
                                {{ scale.min_percentage }}-{{
                                    scale.max_percentage
                                }}%
                            </td>
                            <td>{{ scale.grade_point }}</td>
                            <td>{{ scale.remarks }}</td>
                            <td>
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="edit(scale)"
                                        ><Pencil class="size-4" /></Button
                                    ><Button
                                        variant="ghost"
                                        size="icon"
                                        @click="
                                            router.delete(
                                                `/grading-scales/${scale.id}`,
                                            )
                                        "
                                        ><Trash2 class="size-4"
                                    /></Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table> </CardContent
        ></Card>
    </div>
</template>
