<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Vehicle = {
    id: number;
    vehicle_number: string;
    type: string;
    capacity: number;
    driver_name: string;
    driver_phone: string;
    status: string;
};

const props = defineProps<{ vehicles: Vehicle[]; search: string | null }>();
const editingId = ref<number | null>(null);
const searchForm = useForm({ search: props.search ?? '' });
const form = useForm({
    vehicle_number: '',
    type: 'Bus',
    capacity: 40,
    driver_name: '',
    driver_phone: '',
    status: 'active',
});

const save = () => {
    if (editingId.value) {
        form.put(`/vehicles/${editingId.value}`, {
            onSuccess: () => {
                editingId.value = null;
                form.reset();
                form.type = 'Bus';
                form.capacity = 40;
                form.status = 'active';
            },
        });

        return;
    }

    form.post('/vehicles', { onSuccess: () => form.reset() });
};

const edit = (vehicle: Vehicle) => {
    editingId.value = vehicle.id;
    form.vehicle_number = vehicle.vehicle_number;
    form.type = vehicle.type;
    form.capacity = vehicle.capacity;
    form.driver_name = vehicle.driver_name;
    form.driver_phone = vehicle.driver_phone;
    form.status = vehicle.status;
};

defineOptions({
    layout: { breadcrumbs: [{ title: 'Vehicles', href: '/vehicles' }] },
});
</script>

<template>
    <Head title="Vehicles" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Vehicles"
            description="Manage transport fleet and driver contacts."
        />

        <Card>
            <CardHeader>
                <CardTitle>{{
                    editingId ? 'Edit Vehicle' : 'Add Vehicle'
                }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-3 md:grid-cols-3" @submit.prevent="save">
                    <Input
                        v-model="form.vehicle_number"
                        placeholder="Vehicle number"
                    />
                    <Input v-model="form.type" placeholder="Type" />
                    <Input
                        v-model="form.capacity"
                        type="number"
                        placeholder="Capacity"
                    />
                    <Input
                        v-model="form.driver_name"
                        placeholder="Driver name"
                    />
                    <Input
                        v-model="form.driver_phone"
                        placeholder="Driver phone"
                    />
                    <select
                        v-model="form.status"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <div class="flex gap-2">
                        <Button>{{ editingId ? 'Update' : 'Save' }}</Button>
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
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardContent class="space-y-4 pt-6">
                <form
                    class="flex gap-2"
                    @submit.prevent="
                        router.get('/vehicles', { search: searchForm.search })
                    "
                >
                    <Input
                        v-model="searchForm.search"
                        placeholder="Search vehicle, type, driver"
                    />
                    <Button>Search</Button>
                </form>
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Vehicle</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Driver</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="vehicle in vehicles"
                            :key="vehicle.id"
                            class="border-b"
                        >
                            <td class="py-3 font-medium">
                                {{ vehicle.vehicle_number }}
                            </td>
                            <td>{{ vehicle.type }}</td>
                            <td>{{ vehicle.capacity }}</td>
                            <td>
                                {{ vehicle.driver_name }}
                                <div class="text-xs text-muted-foreground">
                                    {{ vehicle.driver_phone }}
                                </div>
                            </td>
                            <td>{{ vehicle.status }}</td>
                            <td class="text-right">
                                <Button variant="ghost" @click="edit(vehicle)"
                                    >Edit</Button
                                >
                                <Button
                                    variant="ghost"
                                    @click="
                                        router.delete(`/vehicles/${vehicle.id}`)
                                    "
                                >
                                    Delete
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
