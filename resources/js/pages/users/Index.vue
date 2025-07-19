<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Search, Plus, Edit, Trash2, Eye } from 'lucide-vue-next';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    users: {
        data: User[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

const performSearch = () => {
    router.get('/users', { search: search.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const deleteUser = (userId: number) => {
    router.delete(`/users/${userId}`, {
        onSuccess: () => {
            // Success message will be handled by the controller
        },
    });
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString();
};
</script>

<template>
    <Head title="Users" />

    <AppSidebarLayout :breadcrumbs="[
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Users', href: '/users' }
    ]">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Users</h1>
                    <p class="text-muted-foreground">
                        Manage your application users
                    </p>
                </div>
                <Link :href="route('users.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Add User
                    </Button>
                </Link>
            </div>

            <!-- Search and Filters -->
            <Card>
                <CardHeader>
                    <CardTitle>Search Users</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    v-model="search"
                                    placeholder="Search by name or email..."
                                    class="pl-10"
                                    @keyup.enter="performSearch"
                                />
                            </div>
                        </div>
                        <Button @click="performSearch">
                            Search
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Users Table -->
            <Card>
                <CardHeader>
                    <CardTitle>All Users</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-4 font-medium">Name</th>
                                    <th class="text-left p-4 font-medium">Email</th>
                                    <th class="text-left p-4 font-medium">Status</th>
                                    <th class="text-left p-4 font-medium">Created</th>
                                    <th class="text-left p-4 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="border-b hover:bg-muted/50">
                                    <td class="p-4">
                                        <div class="font-medium">{{ user.name }}</div>
                                    </td>
                                    <td class="p-4">{{ user.email }}</td>
                                    <td class="p-4">
                                        <span
                                            :class="user.email_verified_at 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-yellow-100 text-yellow-800'"
                                            class="px-2 py-1 rounded-full text-xs font-medium"
                                        >
                                            {{ user.email_verified_at ? 'Verified' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-muted-foreground">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <Link :href="route('users.show', user.id)">
                                                <Button variant="ghost" size="sm">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="route('users.edit', user.id)">
                                                <Button variant="ghost" size="sm">
                                                    <Edit class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Dialog>
                                                <DialogTrigger as-child>
                                                    <Button variant="ghost" size="sm" class="text-destructive">
                                                        <Trash2 class="h-4 w-4" />
                                                    </Button>
                                                </DialogTrigger>
                                                <DialogContent>
                                                    <DialogHeader>
                                                        <DialogTitle>Delete User</DialogTitle>
                                                        <DialogDescription>
                                                            Are you sure you want to delete {{ user.name }}? This action cannot be undone.
                                                        </DialogDescription>
                                                    </DialogHeader>
                                                    <DialogFooter>
                                                        <Button variant="outline" @click="$event.target.closest('[role=dialog]').close()">
                                                            Cancel
                                                        </Button>
                                                        <Button variant="destructive" @click="deleteUser(user.id)">
                                                            Delete
                                                        </Button>
                                                    </DialogFooter>
                                                </DialogContent>
                                            </Dialog>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="users.links.length > 3" class="flex items-center justify-between mt-6">
                        <div class="text-sm text-muted-foreground">
                            Showing {{ (users.current_page - 1) * users.per_page + 1 }} to 
                            {{ Math.min(users.current_page * users.per_page, users.total) }} of 
                            {{ users.total }} results
                        </div>
                        <div class="flex items-center gap-2">
                            <Link
                                v-for="link in users.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-3 py-2 text-sm rounded-md',
                                    link.active
                                        ? 'bg-primary text-primary-foreground'
                                        : 'text-muted-foreground hover:text-foreground',
                                    !link.url && 'pointer-events-none opacity-50'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppSidebarLayout>
</template> 