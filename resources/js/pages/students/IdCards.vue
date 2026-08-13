<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Printer } from '@lucide/vue';
import { reactive } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

type Option = { id: number; name: string };
type Student = {
    id: number;
    admission_no: string;
    name: string;
    image_url: string;
    full_class_name: string;
    guardian: { name: string; phone: string } | null;
};

const props = defineProps<{
    students: Student[];
    filters: Record<string, string | null>;
    options: { classes: Option[]; sections: Option[] };
}>();

const filters = reactive({
    class_id: props.filters.class_id ?? '',
    section_id: props.filters.section_id ?? '',
});

const applyFilters = () =>
    router.get('/students/id-cards', filters, {
        preserveState: true,
        replace: true,
    });
const printCards = () => window.print();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Student ID Cards', href: '/students/id-cards' },
        ],
    },
});
</script>

<template>
    <Head title="Student ID Cards" />

    <div class="space-y-6 p-4 md:p-6">
        <div class="no-print">
            <ResourcePageHeader
                title="Student ID Cards"
                description="Filter students and print ID cards."
            />
        </div>

        <Card class="no-print">
            <CardContent
                class="flex flex-col gap-3 pt-6 md:flex-row md:items-center"
            >
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
                <Button class="md:ml-auto" @click="printCards">
                    <Printer class="size-4" />
                    Print
                </Button>
            </CardContent>
        </Card>

        <div class="id-card-grid grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="student in students"
                :key="student.id"
                class="id-card rounded-lg border bg-card p-4 shadow-sm"
            >
                <div class="flex items-center gap-4">
                    <img
                        :src="student.image_url"
                        :alt="student.name"
                        class="size-20 rounded-md border object-cover"
                    />
                    <div>
                        <p class="text-xs text-muted-foreground uppercase">
                            Student ID Card
                        </p>
                        <h2 class="text-lg font-semibold">
                            {{ student.name }}
                        </h2>
                        <p class="text-sm">{{ student.admission_no }}</p>
                    </div>
                </div>
                <div class="mt-4 space-y-1 text-sm">
                    <p>
                        <span class="text-muted-foreground">Class:</span>
                        {{ student.full_class_name }}
                    </p>
                    <p>
                        <span class="text-muted-foreground">Guardian:</span>
                        {{ student.guardian?.name }}
                    </p>
                    <p>
                        <span class="text-muted-foreground">Phone:</span>
                        {{ student.guardian?.phone }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    .no-print,
    [data-sidebar='sidebar'],
    header {
        display: none !important;
    }

    .id-card-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12mm !important;
    }

    .id-card {
        break-inside: avoid;
        box-shadow: none !important;
    }
}
</style>
