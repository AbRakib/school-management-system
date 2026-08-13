<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Category = { id: number; name: string; students_count: number };

defineProps<{ categories: Category[] }>();

const editingId = ref<number | null>(null);
const form = useForm({ name: '' });

const submit = () => {
    if (editingId.value) {
        form.put(`/student-categories/${editingId.value}`, {
            onSuccess: () => {
                editingId.value = null;
                form.reset();
            },
        });
        return;
    }

    form.post('/student-categories', { onSuccess: () => form.reset() });
};

const edit = (category: Category) => {
    editingId.value = category.id;
    form.name = category.name;
};

const remove = (category: Category) => {
    if (confirm(`Delete ${category.name}?`)) {
        router.delete(`/student-categories/${category.id}`);
    }
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Student Categories', href: '/student-categories' },
        ],
    },
});
</script>

<template>
    <Head title="Student Categories" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Student Categories"
            description="Create categories such as General, Scholarship, and Special Needs."
        />

        <Card>
            <CardHeader
                ><CardTitle>{{
                    editingId ? 'Edit category' : 'Add category'
                }}</CardTitle></CardHeader
            >
            <CardContent>
                <form
                    class="flex flex-col gap-3 sm:flex-row"
                    @submit.prevent="submit"
                >
                    <div class="flex-1">
                        <Input
                            v-model="form.name"
                            placeholder="Category name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>
                    <Button type="submit" :disabled="form.processing">{{
                        editingId ? 'Update' : 'Add'
                    }}</Button>
                    <Button
                        v-if="editingId"
                        type="button"
                        variant="outline"
                        @click="
                            editingId = null;
                            form.reset();
                        "
                        >Cancel</Button
                    >
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardHeader><CardTitle>Categories</CardTitle></CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="py-3 font-medium">Name</th>
                            <th class="py-3 font-medium">Students</th>
                            <th class="py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="category in categories"
                            :key="category.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3 font-medium">
                                {{ category.name }}
                            </td>
                            <td class="py-3">{{ category.students_count }}</td>
                            <td class="py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="edit(category)"
                                        ><Pencil class="size-4"
                                    /></Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="remove(category)"
                                        ><Trash2 class="size-4"
                                    /></Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
