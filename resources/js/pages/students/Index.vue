<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, IdCard, Pencil, Search, Trash2 } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Option = {
    id: number;
    name: string;
    school_class_id?: number;
    status?: string;
};
type Student = {
    id: number;
    admission_no: string;
    name: string;
    gender: string;
    status: string;
    image_url: string;
    full_class_name: string;
    school_class: { id: number; name: string } | null;
    section: { id: number; name: string } | null;
    guardian: { id: number; name: string; phone: string } | null;
};

const props = defineProps<{
    students: {
        data: Student[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number | null;
        to: number | null;
        total: number;
    };
    filters: Record<string, string | null>;
    options: {
        classes: Option[];
        sections: Option[];
        academicSessions: Option[];
        statuses: string[];
    };
    canManage: boolean;
}>();

const filters = reactive({
    search: props.filters.search ?? '',
    class_id: props.filters.class_id ?? '',
    section_id: props.filters.section_id ?? '',
    academic_session_id: props.filters.academic_session_id ?? '',
    status: props.filters.status ?? '',
});

const applyFilters = () => {
    router.get('/students', filters, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    filters.search = '';
    filters.class_id = '';
    filters.section_id = '';
    filters.academic_session_id = '';
    filters.status = '';
    applyFilters();
};

const remove = (student: Student) => {
    if (confirm(`Delete ${student.name}?`)) {
        router.delete(`/students/${student.id}`);
    }
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Students', href: '/students' }],
    },
});
</script>

<template>
    <Head title="Students" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Students"
            description="Manage admissions, class placement, guardians, and student records."
            :action-label="canManage ? 'Add Student' : undefined"
            :action-href="canManage ? '/students/create' : undefined"
        />

        <Card>
            <CardHeader class="space-y-4">
                <div
                    class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
                >
                    <CardTitle>Student directory</CardTitle>
                    <Button variant="outline" as-child>
                        <Link href="/students/id-cards">
                            <IdCard class="size-4" />
                            ID Cards
                        </Link>
                    </Button>
                </div>
                <div class="grid gap-3 md:grid-cols-5">
                    <div class="relative md:col-span-1">
                        <Search
                            class="absolute top-2.5 left-3 size-4 text-muted-foreground"
                        />
                        <Input
                            v-model="filters.search"
                            class="pl-9"
                            placeholder="Name or admission no"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <select
                        v-model="filters.class_id"
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="">All classes</option>
                        <option
                            v-for="item in options.classes"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                    <select
                        v-model="filters.section_id"
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="">All sections</option>
                        <option
                            v-for="item in options.sections"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.name }}
                        </option>
                    </select>
                    <select
                        v-model="filters.academic_session_id"
                        class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                        @change="applyFilters"
                    >
                        <option value="">All sessions</option>
                        <option
                            v-for="item in options.academicSessions"
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
                        <Button
                            variant="outline"
                            type="button"
                            @click="resetFilters"
                            >Reset</Button
                        >
                    </div>
                </div>
            </CardHeader>
            <CardContent class="overflow-x-auto">
                <table class="w-full min-w-230 text-sm">
                    <thead class="border-b text-left text-muted-foreground">
                        <tr>
                            <th class="px-3 py-3 font-medium">Photo</th>
                            <th class="px-3 py-3 font-medium">Admission No.</th>
                            <th class="px-3 py-3 font-medium">Name</th>
                            <th class="px-3 py-3 font-medium">Class-Section</th>
                            <th class="px-3 py-3 font-medium">Guardian</th>
                            <th class="px-3 py-3 font-medium">Status</th>
                            <th class="px-3 py-3 text-right font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="student in students.data"
                            :key="student.id"
                            class="border-b last:border-0"
                        >
                            <td class="px-3 py-4">
                                <Avatar class="size-10 border">
                                    <AvatarImage
                                        :src="student.image_url"
                                        :alt="student.name"
                                        class="object-cover"
                                    />
                                </Avatar>
                            </td>
                            <td class="px-3 py-4">
                                <Badge variant="secondary">{{
                                    student.admission_no
                                }}</Badge>
                            </td>
                            <td class="px-3 py-4 font-medium">
                                {{ student.name }}
                            </td>
                            <td class="px-3 py-4">
                                {{ student.full_class_name }}
                            </td>
                            <td class="px-3 py-4">
                                <div>{{ student.guardian?.name }}</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ student.guardian?.phone }}
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <Badge variant="outline">{{
                                    student.status
                                }}</Badge>
                            </td>
                            <td class="px-3 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link
                                            :href="`/students/${student.id}`"
                                            aria-label="View student"
                                        >
                                            <Eye class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        v-if="canManage"
                                        variant="ghost"
                                        size="icon"
                                        as-child
                                    >
                                        <Link
                                            :href="`/students/${student.id}/edit`"
                                            aria-label="Edit student"
                                        >
                                            <Pencil class="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        v-if="canManage"
                                        variant="ghost"
                                        size="icon"
                                        aria-label="Delete student"
                                        @click="remove(student)"
                                    >
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td
                                colspan="7"
                                class="px-3 py-12 text-center text-muted-foreground"
                            >
                                No students found.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div
                    class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm text-muted-foreground">
                        Showing {{ students.from ?? 0 }} to
                        {{ students.to ?? 0 }} of {{ students.total }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="link in students.links"
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
