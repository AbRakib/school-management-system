<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type ExamType = { id: number; name: string; weight_percentage: string | null };
defineProps<{ types: ExamType[] }>();
const editingId = ref<number | null>(null);
const form = useForm({ name: '', weight_percentage: '' });
const save = () =>
    editingId.value
        ? form.put(`/exam-types/${editingId.value}`, {
              onSuccess: () => {
                  editingId.value = null;
                  form.reset();
              },
          })
        : form.post('/exam-types', { onSuccess: () => form.reset() });
const edit = (type: ExamType) => {
    editingId.value = type.id;
    form.name = type.name;
    form.weight_percentage = type.weight_percentage ?? '';
};
defineOptions({
    layout: { breadcrumbs: [{ title: 'Exam Types', href: '/exam-types' }] },
});
</script>
<template>
    <Head title="Exam Types" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Exam Types"
            description="Manage exam categories and optional grade weights."
        />
        <Card
            ><CardContent class="space-y-4 pt-6">
                <form
                    class="grid gap-3 md:grid-cols-[1fr_180px_auto]"
                    @submit.prevent="save"
                >
                    <Input
                        v-model="form.name"
                        placeholder="Exam type name"
                        required
                    />
                    <Input
                        v-model="form.weight_percentage"
                        type="number"
                        step="0.01"
                        placeholder="Weight %"
                    />
                    <Button>{{ editingId ? 'Update' : 'Add' }}</Button>
                </form>
                <table class="w-full text-sm">
                    <tbody>
                        <tr
                            v-for="type in types"
                            :key="type.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3 font-medium">{{ type.name }}</td>
                            <td class="py-3">
                                {{ type.weight_percentage ?? 'N/A' }}
                            </td>
                            <td class="py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="edit(type)"
                                        ><Pencil class="size-4" /></Button
                                    ><Button
                                        variant="ghost"
                                        size="icon"
                                        @click="
                                            router.delete(
                                                `/exam-types/${type.id}`,
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
