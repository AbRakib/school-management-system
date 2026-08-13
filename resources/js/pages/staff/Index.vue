<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ClipboardCheck, Eye, Pencil, Search, Trash2 } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Option = { id: number; name: string };
type Staff = {
    id: number;
    staff_id: string;
    name: string;
    phone: string;
    status: string;
    photo_url: string;
    department: Option;
    designation: Option & { is_teaching_staff: boolean };
};

const props = defineProps<{
    staff: {
        data: Staff[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number | null;
        to: number | null;
        total: number;
    };
    filters: Record<string, string | null>;
    options: {
        departments: Option[];
        designations: Option[];
        statuses: string[];
    };
}>();

const filters = reactive({
    search: props.filters.search ?? '',
    department_id: props.filters.department_id ?? '',
    designation_id: props.filters.designation_id ?? '',
    status: props.filters.status ?? '',
});

const applyFilters = () =>
    router.get('/staff', filters, { preserveState: true, replace: true });
const resetFilters = () => {
    filters.search = '';
    filters.department_id = '';
    filters.designation_id = '';
    filters.status = '';
    applyFilters();
};

const remove = (staffMember: Staff) => {
    if (confirm(`Delete ${staffMember.name}?`)) {
        router.delete(`/staff/${staffMember.id}`);
    }
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Staff', href: '/staff' }],
    },
});
</script>

<template>
    <Head title="Staff" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Staff"
            description="Manage teaching and non-teaching staff records."
            action-label="Add Staff"
            action-href="/staff/create"
        />

        <Card>
            <CardHeader class="space-y-4">
                <div
                    class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
                >
                    <CardTitle>Staff directory</CardTitle>
                    <Button variant="outline" as-child>
                        <Link href="/departments"
                            >Departments / Designations</Link
                        >
                    </Button>
                </div>
                <div class="grid gap-3 md:grid-cols-5">
                    <div class="relative md:col-span-2">
                        <Search
                            class="absolute top-2.5 left-3 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="filters.search"
                            class="pl-9"
                            placeholder="Search by name"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <select
                        v-model="filters.department_id"
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="">All departments</option>
                        <option
                            v-for="item in options.departments"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                    <select
                        v-model="filters.designation_id"
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="">All designations</option>
                        <option
                            v-for="item in options.designations"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                    <div class="flex gap-2">
                        <select
                            v-model="filters.status"
                            class="h-9 flex-1 rounded-md border border-input bg-transparent px-3 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">All status</option>
                            <option
                                v-for="status in options.statuses"
                                :key="status"
                                :value="status"
                            >
                                {{ status }}
                            </option>
                        </select>
                        <Button variant="outline" @click="resetFilters"
                            >Reset</Button
                        >
                    </div>
                </div>
            </CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-220 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">Photo</th>
                            <th class="px-3 py-3 font-medium">Staff ID</th>
                            <th class="px-3 py-3 font-medium">Name</th>
                            <th class="px-3 py-3 font-medium">Department</th>
                            <th class="px-3 py-3 font-medium">Designation</th>
                            <th class="px-3 py-3 font-medium">Phone</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-3 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="staffMember in staff.data"
                            :key="staffMember.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-3 py-4">
                                <Avatar class="size-10 border"
                                    ><AvatarImage
                                        :src="staffMember.photo_url"
                                        :alt="staffMember.name"
                                        class="object-cover"
                                /></Avatar>
                            </td>
                            <td class="px-3 py-4">
                                <Badge variant="secondary">{{
                                    staffMember.staff_id
                                }}</Badge>
                            </td>
                            <td class="px-3 py-4 font-medium">
                                {{ staffMember.name }}
                            </td>
                            <td class="px-3 py-4">
                                {{ staffMember.department.name }}
                            </td>
                            <td class="px-3 py-4">
                                {{ staffMember.designation.name }}
                            </td>
                            <td class="px-3 py-4">{{ staffMember.phone }}</td>
                            <td class="px-3 py-4">
                                <Badge variant="outline">{{
                                    staffMember.status
                                }}</Badge>
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="icon" as-child
                                        ><Link
                                            :href="`/staff/${staffMember.id}`"
                                            ><Eye class="size-4" /></Link
                                    ></Button>
                                    <Button variant="ghost" size="icon" as-child
                                        ><Link
                                            :href="`/staff/${staffMember.id}/assignments`"
                                            ><ClipboardCheck
                                                class="size-4" /></Link
                                    ></Button>
                                    <Button variant="ghost" size="icon" as-child
                                        ><Link
                                            :href="`/staff/${staffMember.id}/edit`"
                                            ><Pencil class="size-4" /></Link
                                    ></Button>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        @click="remove(staffMember)"
                                        ><Trash2 class="size-4"
                                    /></Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="staff.data.length === 0">
                            <td
                                colspan="8"
                                class="px-3 py-12 text-center text-muted-foreground"
                            >
                                No staff found.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div
                    class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm text-muted-foreground">
                        Showing {{ staff.from ?? 0 }} to {{ staff.to ?? 0 }} of
                        {{ staff.total }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="link in staff.links"
                            :key="link.label"
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                            :disabled="!link.url"
                            @click="link.url && router.visit(link.url)"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
