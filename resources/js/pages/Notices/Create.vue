<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type SchoolClass = { id: number; name: string; grade_level: number | null };

defineProps<{ classes: SchoolClass[] }>();

const form = useForm({
    title: '',
    description: '',
    audience: 'all',
    class_id: '',
    publish_date: new Date().toISOString().slice(0, 10),
    expiry_date: '',
    attachment: null as File | null,
});

const submit = () => {
    form.post('/notices', { forceFormData: true });
};

defineOptions({
    layout: { breadcrumbs: [{ title: 'Add Notice', href: '/notices/create' }] },
});
</script>

<template>
    <Head title="Add Notice" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Add Notice"
            description="Publish an announcement for the selected audience."
        />

        <Card>
            <CardContent class="pt-6">
                <form class="grid gap-4" @submit.prevent="submit">
                    <Input v-model="form.title" placeholder="Title" />
                    <textarea
                        v-model="form.description"
                        class="min-h-44 rounded-md border bg-transparent p-3 text-sm"
                        placeholder="Description"
                    />
                    <div class="grid gap-3 md:grid-cols-2">
                        <select
                            v-model="form.audience"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        >
                            <option value="all">All</option>
                            <option value="students">Students</option>
                            <option value="staff">Staff</option>
                            <option value="specific_class">
                                Specific class
                            </option>
                        </select>
                        <select
                            v-if="form.audience === 'specific_class'"
                            v-model="form.class_id"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        >
                            <option value="">Class</option>
                            <option
                                v-for="schoolClass in classes"
                                :key="schoolClass.id"
                                :value="schoolClass.id"
                            >
                                {{ schoolClass.name }}
                            </option>
                        </select>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        <Input v-model="form.publish_date" type="date" />
                        <Input v-model="form.expiry_date" type="date" />
                    </div>
                    <Input
                        type="file"
                        @change="
                            form.attachment =
                                ($event.target as HTMLInputElement)
                                    .files?.[0] ?? null
                        "
                    />
                    <div class="flex gap-2">
                        <Button>Publish Notice</Button>
                        <Button variant="outline" type="button" as-child>
                            <Link href="/notices">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
