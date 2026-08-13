<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
type Period = {
    id: number;
    name: string;
    start_time: string;
    end_time: string;
};
defineProps<{ periods: Period[] }>();
const editing = ref<number | null>(null);
const form = useForm({ name: '', start_time: '', end_time: '' });
const save = () =>
    editing.value
        ? form.put(`/timetable/periods/${editing.value}`, {
              onSuccess: () => {
                  editing.value = null;
                  form.reset();
              },
          })
        : form.post('/timetable/periods', { onSuccess: () => form.reset() });
const edit = (p: Period) => {
    editing.value = p.id;
    form.name = p.name;
    form.start_time = p.start_time.slice(0, 5);
    form.end_time = p.end_time.slice(0, 5);
};
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Timetable Periods', href: '/timetable/periods' },
        ],
    },
});
</script>
<template>
    <Head title="Timetable Periods" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Timetable Periods"
            description="Define the daily period structure shared by all classes."
        /><Card
            ><CardContent class="space-y-4 pt-6"
                ><form
                    class="grid gap-3 md:grid-cols-[1fr_160px_160px_auto]"
                    @submit.prevent="save"
                >
                    <Input v-model="form.name" placeholder="Period 1" /><Input
                        v-model="form.start_time"
                        type="time"
                    /><Input v-model="form.end_time" type="time" /><Button>{{
                        editing ? 'Update' : 'Add'
                    }}</Button>
                </form>
                <table class="w-full text-sm">
                    <tbody>
                        <tr v-for="p in periods" :key="p.id" class="border-b">
                            <td class="py-3 font-medium">{{ p.name }}</td>
                            <td>{{ p.start_time }} - {{ p.end_time }}</td>
                            <td class="text-right">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    @click="edit(p)"
                                    ><Pencil class="size-4" /></Button
                                ><Button
                                    variant="ghost"
                                    size="icon"
                                    @click="
                                        router.delete(
                                            `/timetable/periods/${p.id}`,
                                        )
                                    "
                                    ><Trash2 class="size-4"
                                /></Button>
                            </td>
                        </tr>
                    </tbody></table></CardContent
        ></Card>
    </div>
</template>
