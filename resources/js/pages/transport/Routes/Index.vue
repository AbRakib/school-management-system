<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';

type Vehicle = { id: number; vehicle_number: string; type: string };
type Stop = {
    id?: number;
    name: string;
    pickup_time: string | null;
    order: number;
};
type TransportRoute = {
    id: number;
    name: string;
    vehicle_id: number;
    fare_amount: string | number;
    vehicle: Vehicle;
    stops: Stop[];
};

const props = defineProps<{
    routes: TransportRoute[];
    vehicles: Vehicle[];
    search: string | null;
}>();

const editingId = ref<number | null>(null);
const expandedId = ref<number | null>(null);
const searchForm = useForm({ search: props.search ?? '' });
const form = useForm({
    name: '',
    vehicle_id: '',
    fare_amount: '',
    stops: [{ name: '', pickup_time: '', order: 1 }],
});

const resetForm = () => {
    editingId.value = null;
    form.reset();
    form.stops = [{ name: '', pickup_time: '', order: 1 }];
};

const addStop = () => {
    form.stops.push({
        name: '',
        pickup_time: '',
        order: form.stops.length + 1,
    });
};

const removeStop = (index: number) => {
    form.stops.splice(index, 1);
    form.stops.forEach((stop, order) => (stop.order = order + 1));
};

const edit = (route: TransportRoute) => {
    editingId.value = route.id;
    form.name = route.name;
    form.vehicle_id = String(route.vehicle_id);
    form.fare_amount = String(route.fare_amount);
    form.stops = route.stops.length
        ? route.stops.map((stop, index) => ({
              name: stop.name,
              pickup_time: stop.pickup_time ?? '',
              order: stop.order ?? index + 1,
          }))
        : [{ name: '', pickup_time: '', order: 1 }];
};

const save = () => {
    if (editingId.value) {
        form.put(`/transport-routes/${editingId.value}`, {
            onSuccess: resetForm,
        });

        return;
    }

    form.post('/transport-routes', { onSuccess: resetForm });
};

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Transport Routes', href: '/transport-routes' }],
    },
});
</script>

<template>
    <Head title="Transport Routes" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Transport Routes"
            description="Manage routes, assigned vehicles, fares, and ordered stops."
        />

        <Card>
            <CardHeader>
                <CardTitle>{{
                    editingId ? 'Edit Route' : 'Add Route'
                }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-4" @submit.prevent="save">
                    <div class="grid gap-3 md:grid-cols-3">
                        <Input v-model="form.name" placeholder="Route name" />
                        <select
                            v-model="form.vehicle_id"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        >
                            <option value="">Vehicle</option>
                            <option
                                v-for="vehicle in vehicles"
                                :key="vehicle.id"
                                :value="vehicle.id"
                            >
                                {{ vehicle.vehicle_number }} -
                                {{ vehicle.type }}
                            </option>
                        </select>
                        <Input
                            v-model="form.fare_amount"
                            type="number"
                            placeholder="Fare amount"
                        />
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium">Stops</h3>
                            <Button
                                type="button"
                                variant="outline"
                                @click="addStop"
                                >Add Stop</Button
                            >
                        </div>
                        <div
                            v-for="(stop, index) in form.stops"
                            :key="index"
                            class="grid gap-2 md:grid-cols-[1fr_160px_90px_auto]"
                        >
                            <Input
                                v-model="stop.name"
                                placeholder="Stop name"
                            />
                            <Input v-model="stop.pickup_time" type="time" />
                            <Input v-model="stop.order" type="number" />
                            <Button
                                type="button"
                                variant="ghost"
                                :disabled="form.stops.length === 1"
                                @click="removeStop(index)"
                                >Remove</Button
                            >
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Button>{{
                            editingId ? 'Update Route' : 'Save Route'
                        }}</Button>
                        <Button
                            v-if="editingId"
                            type="button"
                            variant="outline"
                            @click="resetForm"
                        >
                            Cancel
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardContent class="space-y-4 pt-6">
                <form
                    class="flex gap-2"
                    @submit.prevent="
                        router.get('/transport-routes', {
                            search: searchForm.search,
                        })
                    "
                >
                    <Input
                        v-model="searchForm.search"
                        placeholder="Search route"
                    />
                    <Button>Search</Button>
                </form>

                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Route</th>
                            <th>Vehicle</th>
                            <th>Fare</th>
                            <th>Stops</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="route in routes" :key="route.id">
                            <tr class="border-b">
                                <td class="py-3 font-medium">
                                    {{ route.name }}
                                </td>
                                <td>{{ route.vehicle.vehicle_number }}</td>
                                <td>{{ route.fare_amount }}</td>
                                <td>{{ route.stops.length }}</td>
                                <td class="text-right">
                                    <Button
                                        variant="ghost"
                                        @click="
                                            expandedId =
                                                expandedId === route.id
                                                    ? null
                                                    : route.id
                                        "
                                        >Manage Stops</Button
                                    >
                                    <Button variant="ghost" @click="edit(route)"
                                        >Edit</Button
                                    >
                                    <Button
                                        variant="ghost"
                                        @click="
                                            router.delete(
                                                `/transport-routes/${route.id}`,
                                            )
                                        "
                                        >Delete</Button
                                    >
                                </td>
                            </tr>
                            <tr v-if="expandedId === route.id">
                                <td colspan="5" class="bg-muted/30 p-4">
                                    <div class="grid gap-2 md:grid-cols-2">
                                        <div
                                            v-for="stop in route.stops"
                                            :key="`${route.id}-${stop.order}-${stop.name}`"
                                            class="rounded border bg-background p-3"
                                        >
                                            <div class="font-medium">
                                                {{ stop.order }}.
                                                {{ stop.name }}
                                            </div>
                                            <div
                                                class="text-xs text-muted-foreground"
                                            >
                                                {{
                                                    stop.pickup_time ||
                                                    'No pickup time'
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </CardContent>
        </Card>
    </div>
</template>
