<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ResourcePageHeader from '@/components/ResourcePageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
type Category = { id: number; name: string };
type Book = {
    id: number;
    title: string;
    author: string;
    isbn: string | null;
    publisher: string | null;
    total_copies: number;
    available_copies: number;
    shelf_location: string | null;
    book_category_id: number;
    category: Category;
};
const props = defineProps<{
    books: Book[];
    categories: Category[];
    search: string | null;
}>();
const cat = useForm({ name: '' });
const form = useForm({
    book_category_id: '',
    title: '',
    author: '',
    isbn: '',
    publisher: '',
    total_copies: 1,
    available_copies: '',
    shelf_location: '',
});
const searchForm = useForm({ search: props.search ?? '' });
const editingId = ref<number | null>(null);
const editForm = useForm({
    book_category_id: '',
    title: '',
    author: '',
    isbn: '',
    publisher: '',
    total_copies: 1,
    available_copies: 1,
    shelf_location: '',
});
const save = () => form.post('/books', { onSuccess: () => form.reset() });
const startEdit = (book: Book) => {
    editingId.value = book.id;
    editForm.book_category_id = String(book.book_category_id);
    editForm.title = book.title;
    editForm.author = book.author;
    editForm.isbn = book.isbn ?? '';
    editForm.publisher = book.publisher ?? '';
    editForm.total_copies = book.total_copies;
    editForm.available_copies = book.available_copies;
    editForm.shelf_location = book.shelf_location ?? '';
};
const updateBook = () => {
    if (!editingId.value) {
        return;
    }

    editForm.put(`/books/${editingId.value}`, {
        onSuccess: () => {
            editingId.value = null;
            editForm.reset();
        },
    });
};
defineOptions({
    layout: { breadcrumbs: [{ title: 'Books', href: '/books' }] },
});
</script>
<template>
    <Head title="Books" />
    <div class="space-y-6 p-4 md:p-6">
        <ResourcePageHeader
            title="Book Catalog"
            description="Manage library books, categories, and copy availability."
        />
        <div class="grid gap-6 xl:grid-cols-3">
            <Card
                ><CardHeader><CardTitle>Categories</CardTitle></CardHeader
                ><CardContent class="space-y-3"
                    ><form
                        class="flex gap-2"
                        @submit.prevent="
                            cat.post('/book-categories', {
                                onSuccess: () => cat.reset(),
                            })
                        "
                    >
                        <Input
                            v-model="cat.name"
                            placeholder="Category"
                        /><Button>Add</Button>
                    </form>
                    <div
                        v-for="c in categories"
                        :key="c.id"
                        class="border-b py-2"
                    >
                        {{ c.name }}
                    </div></CardContent
                ></Card
            ><Card class="xl:col-span-2"
                ><CardHeader><CardTitle>Add Book</CardTitle></CardHeader
                ><CardContent
                    ><form
                        class="grid gap-3 md:grid-cols-2"
                        @submit.prevent="save"
                    >
                        <select
                            v-model="form.book_category_id"
                            class="h-9 rounded-md border bg-transparent px-3 text-sm"
                        >
                            <option value="">Category</option>
                            <option
                                v-for="c in categories"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name }}
                            </option></select
                        ><Input
                            v-model="form.title"
                            placeholder="Title"
                        /><Input
                            v-model="form.author"
                            placeholder="Author"
                        /><Input v-model="form.isbn" placeholder="ISBN" /><Input
                            v-model="form.publisher"
                            placeholder="Publisher"
                        /><Input
                            v-model="form.total_copies"
                            type="number"
                            placeholder="Total copies"
                        /><Input
                            v-model="form.shelf_location"
                            placeholder="Shelf"
                        /><Button>Save Book</Button>
                    </form></CardContent
                ></Card
            >
        </div>
        <Card
            ><CardContent class="space-y-4 pt-6"
                ><form
                    class="flex gap-2"
                    @submit.prevent="
                        router.get('/books', { search: searchForm.search })
                    "
                >
                    <Input
                        v-model="searchForm.search"
                        placeholder="Search title, author, ISBN"
                    /><Button>Search</Button>
                </form>
                <form
                    v-if="editingId"
                    class="grid gap-3 rounded border p-4 md:grid-cols-3"
                    @submit.prevent="updateBook"
                >
                    <select
                        v-model="editForm.book_category_id"
                        class="h-9 rounded-md border bg-transparent px-3 text-sm"
                    >
                        <option value="">Category</option>
                        <option
                            v-for="c in categories"
                            :key="c.id"
                            :value="c.id"
                        >
                            {{ c.name }}
                        </option></select
                    ><Input
                        v-model="editForm.title"
                        placeholder="Title"
                    /><Input
                        v-model="editForm.author"
                        placeholder="Author"
                    /><Input v-model="editForm.isbn" placeholder="ISBN" /><Input
                        v-model="editForm.publisher"
                        placeholder="Publisher"
                    /><Input
                        v-model="editForm.total_copies"
                        type="number"
                        placeholder="Total copies"
                    /><Input
                        v-model="editForm.available_copies"
                        type="number"
                        placeholder="Available copies"
                    /><Input
                        v-model="editForm.shelf_location"
                        placeholder="Shelf"
                    />
                    <div class="flex gap-2">
                        <Button>Update</Button>
                        <Button
                            type="button"
                            variant="outline"
                            @click="editingId = null"
                            >Cancel</Button
                        >
                    </div>
                </form>
                <table class="w-full min-w-180 text-sm">
                    <thead class="border-b text-left">
                        <tr>
                            <th class="py-3">Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Copies</th>
                            <th>Shelf</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="b in books" :key="b.id" class="border-b">
                            <td class="py-3 font-medium">{{ b.title }}</td>
                            <td>{{ b.author }}</td>
                            <td>{{ b.category.name }}</td>
                            <td>
                                {{ b.available_copies }}/{{ b.total_copies }}
                            </td>
                            <td>{{ b.shelf_location }}</td>
                            <td class="text-right">
                                <Button variant="ghost" @click="startEdit(b)"
                                    >Edit</Button
                                >
                                <Button
                                    variant="ghost"
                                    @click="router.delete(`/books/${b.id}`)"
                                    >Delete</Button
                                >
                            </td>
                        </tr>
                    </tbody>
                </table></CardContent
            ></Card
        >
    </div>
</template>
