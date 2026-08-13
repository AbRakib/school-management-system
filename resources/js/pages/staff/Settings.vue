<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Department = { id: number; name: string; staff_count: number };
type Designation = {
    id: number;
    name: string;
    is_teaching_staff: boolean;
    staff_count: number;
};

defineProps<{ departments: Department[]; designations: Designation[] }>();

const activeTab = ref<'departments' | 'designations'>('departments');
const editingDepartmentId = ref<number | null>(null);
const editingDesignationId = ref<number | null>(null);
const departmentForm = useForm({ name: '' });
const designationForm = useForm({ name: '', is_teaching_staff: false });

const saveDepartment = () => {
    if (editingDepartmentId.value) {
        departmentForm.put(`/departments/${editingDepartmentId.value}`, {
            onSuccess: () => {
                editingDepartmentId.value = null;
                departmentForm.reset();
            },
        });
        return;
    }
    departmentForm.post('/departments', {
        onSuccess: () => departmentForm.reset(),
    });
};
const saveDesignation = () => {
    if (editingDesignationId.value) {
        designationForm.put(`/designations/${editingDesignationId.value}`, {
            onSuccess: () => {
                editingDesignationId.value = null;
                designationForm.reset();
            },
        });
        return;
    }
    designationForm.post('/designations', {
        onSuccess: () => designationForm.reset(),
    });
};
const editDepartment = (department: Department) => {
    editingDepartmentId.value = department.id;
    departmentForm.name = department.name;
};
const editDesignation = (designation: Designation) => {
    editingDesignationId.value = designation.id;
    designationForm.name = designation.name;
    designationForm.is_teaching_staff = designation.is_teaching_staff;
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Staff Settings', href: '/departments' }],
    },
});
</script>

<template>
    <Head title="Staff Settings" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Departments & Designations"
            description="Manage staff departments and job titles from one settings page."
        />

        <div class="flex gap-2">
            <Button
                :variant="activeTab === 'departments' ? 'default' : 'outline'"
                @click="activeTab = 'departments'"
                >Departments</Button
            >
            <Button
                :variant="activeTab === 'designations' ? 'default' : 'outline'"
                @click="activeTab = 'designations'"
                >Designations</Button
            >
        </div>

        <Card v-if="activeTab === 'departments'">
            <CardHeader><CardTitle>Departments</CardTitle></CardHeader>
            <CardContent class="space-y-5">
                <form
                    class="flex flex-col gap-3 sm:flex-row"
                    @submit.prevent="saveDepartment"
                >
                    <div class="flex-1">
                        <Input
                            v-model="departmentForm.name"
                            placeholder="Department name"
                        />
                        <InputError :message="departmentForm.errors.name" />
                    </div>
                    <Button type="submit">{{
                        editingDepartmentId ? 'Update' : 'Add'
                    }}</Button>
                </form>
                <table class="w-full text-sm">
                    <tbody>
                        <tr
                            v-for="department in departments"
                            :key="department.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3 font-medium">
                                {{ department.name }}
                            </td>
                            <td class="py-3 text-muted-foreground">
                                {{ department.staff_count }} staff
                            </td>
                            <td class="py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="editDepartment(department)"
                                        ><Pencil class="size-4"
                                    /></Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="
                                            router.delete(
                                                `/departments/${department.id}`,
                                            )
                                        "
                                        ><Trash2 class="size-4"
                                    /></Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>

        <Card v-else>
            <CardHeader><CardTitle>Designations</CardTitle></CardHeader>
            <CardContent class="space-y-5">
                <form
                    class="grid gap-3 sm:grid-cols-[1fr_auto_auto]"
                    @submit.prevent="saveDesignation"
                >
                    <div>
                        <Input
                            v-model="designationForm.name"
                            placeholder="Designation name"
                        />
                        <InputError :message="designationForm.errors.name" />
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input
                            v-model="designationForm.is_teaching_staff"
                            type="checkbox"
                        />
                        Teaching staff
                    </label>
                    <Button type="submit">{{
                        editingDesignationId ? 'Update' : 'Add'
                    }}</Button>
                </form>
                <table class="w-full text-sm">
                    <tbody>
                        <tr
                            v-for="designation in designations"
                            :key="designation.id"
                            class="border-b last:border-0"
                        >
                            <td class="py-3 font-medium">
                                {{ designation.name }}
                            </td>
                            <td class="py-3 text-muted-foreground">
                                {{
                                    designation.is_teaching_staff
                                        ? 'Teaching'
                                        : 'Non-teaching'
                                }}
                            </td>
                            <td class="py-3 text-muted-foreground">
                                {{ designation.staff_count }} staff
                            </td>
                            <td class="py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="editDesignation(designation)"
                                        ><Pencil class="size-4"
                                    /></Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="
                                            router.delete(
                                                `/designations/${designation.id}`,
                                            )
                                        "
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
