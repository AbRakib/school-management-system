<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type SchoolClass = { id: number; name: string; grade_level: number | null };
type Notice = {
    id: number;
    title: string;
    description: string;
    audience: string;
    class_id: number | null;
    publish_date: string;
    expiry_date: string | null;
    attachment: string | null;
};

const props = defineProps<{ classes: SchoolClass[]; notice: Notice }>();

const form = useForm({
    title: props.notice.title,
    description: props.notice.description,
    audience: props.notice.audience,
    class_id: props.notice.class_id ? String(props.notice.class_id) : '',
    publish_date: props.notice.publish_date,
    expiry_date: props.notice.expiry_date ?? '',
    attachment: null as File | null,
});

const submit = () => {
    form.transform((data) => ({ ...data, _method: 'put' })).post(
        `/notices/${props.notice.id}`,
        {
            forceFormData: true,
        },
    );
};

defineOptions({
    layout: { breadcrumbs: [{ title: 'Edit Notice', href: '/notices' }] },
});
</script>

<template>
    <Head title="Edit Notice" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Edit Notice"
            description="Update announcement details and audience."
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
                    <div class="space-y-2">
                        <Input
                            type="file"
                            @change="
                                form.attachment =
                                    ($event.target as HTMLInputElement)
                                        .files?.[0] ?? null
                            "
                        />
                        <p
                            v-if="notice.attachment"
                            class="text-xs text-muted-foreground"
                        >
                            Existing attachment will remain unless a new file is
                            selected.
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <Button>Update Notice</Button>
                        <Button variant="outline" type="button" as-child>
                            <Link href="/notices">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
