<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import ProfileImageInput from '@/components/ProfileImageInput.vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Guardian = {
    id: number;
    name: string;
    relationship: string;
    phone: string;
    email: string | null;
    occupation: string | null;
    address: string | null;
    image_url: string;
};

const props = defineProps<{ guardian?: Guardian }>();
const editing = Boolean(props.guardian);

const form = useForm({
    name: props.guardian?.name ?? '',
    relationship: props.guardian?.relationship ?? 'Father',
    phone: props.guardian?.phone ?? '',
    email: props.guardian?.email ?? '',
    occupation: props.guardian?.occupation ?? '',
    address: props.guardian?.address ?? '',
    image: null as File | null,
});

const submit = () => {
    if (props.guardian) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/guardians/${props.guardian.id}`,
            { forceFormData: true },
        );

        return;
    }

    form.post('/guardians', { forceFormData: true });
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Parents', href: '/guardians' }],
    },
});
</script>

<template>
    <Head :title="editing ? 'Edit parent' : 'Add parent'" />

    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            :title="editing ? 'Edit parent' : 'Add parent'"
            description="Enter the parent or guardian's contact details."
        />

        <Card class="max-w-3xl">
            <CardContent class="pt-6">
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <ProfileImageInput
                            v-model="form.image"
                            :current-image-url="guardian?.image_url"
                            default-image-url="/images/default-parent.png"
                            attribution-url="https://www.flaticon.com/free-icon/family_547562"
                            attribution-label="Parent icon by Freepik - Flaticon"
                            label="Parent image"
                        />
                        <InputError :message="form.errors.image" />
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="name">Full name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="relationship">Relationship</Label>
                            <select
                                id="relationship"
                                v-model="form.relationship"
                                class="h-9 rounded-md border border-input bg-transparent px-3 text-sm"
                            >
                                <option>Father</option>
                                <option>Mother</option>
                                <option>Guardian</option>
                            </select>
                            <InputError :message="form.errors.relationship" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="phone">Phone</Label>
                            <Input id="phone" v-model="form.phone" required />
                            <InputError :message="form.errors.phone" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                            />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="occupation">Occupation</Label>
                            <Input id="occupation" v-model="form.occupation" />
                            <InputError :message="form.errors.occupation" />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="address">Address</Label>
                        <textarea
                            id="address"
                            v-model="form.address"
                            rows="3"
                            class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                        />
                        <InputError :message="form.errors.address" />
                    </div>
                    <div class="flex gap-3">
                        <Button type="submit" :disabled="form.processing">
                            {{ editing ? 'Update parent' : 'Save parent' }}
                        </Button>
                        <Button variant="outline" as-child>
                            <Link href="/guardians">Cancel</Link>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
