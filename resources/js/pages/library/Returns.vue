<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
const props = defineProps<{ issues: any[]; search: string | null }>();
const form = useForm({ search: props.search ?? '' });
const fine = (due: string) =>
    Math.max(Math.ceil((Date.now() - new Date(due).getTime()) / 86400000), 0) *
    10;
defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Return Books', href: '/library/returns' }],
    },
});
</script>
<template>
    <Head title="Return Books" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Issue / Return"
            description="Find active issues and return books."
        /><Card
            ><CardContent class="space-y-4 pt-6"
                ><form
                    class="flex gap-2"
                    @submit.prevent="
                        router.get('/library/returns', { search: form.search })
                    "
                >
                    <Input
                        v-model="form.search"
                        placeholder="Search active issues by book"
                    /><Button>Search</Button>
                </form>
                <table class="w-full min-w-180 text-sm">
                    <tbody>
                        <tr v-for="i in issues" :key="i.id" class="border-b">
                            <td class="py-3 font-medium">{{ i.book.title }}</td>
                            <td>{{ i.borrower.name }}</td>
                            <td>{{ i.due_date }}</td>
                            <td>{{ i.status }}</td>
                            <td>Fine: {{ fine(i.due_date) }}</td>
                            <td class="text-right">
                                <Button
                                    @click="
                                        router.post(
                                            `/library/issues/${i.id}/return`,
                                        )
                                    "
                                    >Return</Button
                                >
                            </td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
    </div>
</template>
