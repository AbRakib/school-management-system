<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
const props = defineProps<{ books: any[]; borrowers: any[]; filters: any }>();
const search = useForm({
    book_search: props.filters.bookSearch ?? '',
    borrower_search: props.filters.borrowerSearch ?? '',
    borrower_type: props.filters.borrowerType ?? 'student',
});
const form = useForm({
    book_id: '',
    borrower_type: search.borrower_type,
    borrower_id: '',
    issue_date: new Date().toISOString().slice(0, 10),
    due_date: '',
});
const load = () =>
    router.get(
        '/library/issue',
        {
            book_search: search.book_search,
            borrower_search: search.borrower_search,
            borrower_type: search.borrower_type,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
const submit = () => {
    form.borrower_type = search.borrower_type;
    form.post('/library/issue', { onSuccess: () => form.reset() });
};
defineOptions({
    layout: { breadcrumbs: [{ title: 'Issue Book', href: '/library/issue' }] },
});
</script>
<template>
    <Head title="Issue Book" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Issue Book"
            description="Search and select a book and borrower, then issue in one transaction."
        />
        <div class="flex gap-2">
            <Button variant="outline" @click="router.get('/library/returns')"
                >Return Books</Button
            >
            <Button variant="outline" @click="router.get('/library/due')"
                >Due Books</Button
            >
        </div>
        <Card
            ><CardContent class="grid gap-3 pt-6 md:grid-cols-3"
                ><Input
                    v-model="search.book_search"
                    placeholder="Search book"
                    @keyup.enter="load" /><select
                    v-model="search.borrower_type"
                    class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    @change="load"
                >
                    <option value="student">Student</option>
                    <option value="staff">Staff</option></select
                ><Input
                    v-model="search.borrower_search"
                    placeholder="Search borrower"
                    @keyup.enter="load" /></CardContent></Card
        ><Card
            ><CardContent class="grid gap-5 pt-6 md:grid-cols-2"
                ><div>
                    <h3 class="mb-2 font-medium">Books</h3>
                    <button
                        v-for="b in books"
                        :key="b.id"
                        class="mb-2 block w-full rounded border p-3 text-left"
                        @click="form.book_id = b.id"
                    >
                        {{ b.title }}
                        <span class="text-xs text-muted-foreground"
                            >({{ b.available_copies }} available)</span
                        >
                    </button>
                </div>
                <div>
                    <h3 class="mb-2 font-medium">Borrowers</h3>
                    <button
                        v-for="b in borrowers"
                        :key="b.id"
                        class="mb-2 block w-full rounded border p-3 text-left"
                        @click="form.borrower_id = b.id"
                    >
                        {{ b.name }}
                        <span class="text-xs text-muted-foreground">{{
                            b.admission_no || b.staff_id
                        }}</span>
                    </button>
                </div>
                <Input v-model="form.issue_date" type="date" /><Input
                    v-model="form.due_date"
                    type="date"
                /><Button class="md:col-span-2" @click="submit"
                    >Issue Book</Button
                ></CardContent
            ></Card
        >
    </div>
</template>
