<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Edit, Mail, Calendar, Shield } from 'lucide-vue-next';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    user: User;
}

defineProps<Props>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="User Details" />

    <AppSidebarLayout :breadcrumbs="[
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Users', href: '/users' },
        { title: user.name, href: `/users/${user.id}` }
    ]">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" size="sm" @click="$inertia.visit(route('users.index'))">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Back to Users
                    </Button>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">{{ user.name }}</h1>
                        <p class="text-muted-foreground">
                            User details and information
                        </p>
                    </div>
                </div>
                <Link :href="route('users.edit', user.id)">
                    <Button>
                        <Edit class="mr-2 h-4 w-4" />
                        Edit User
                    </Button>
                </Link>
            </div>

            <!-- User Information -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Basic Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center gap-3">
                            <Mail class="h-4 w-4 text-muted-foreground" />
                            <div>
                                <p class="text-sm font-medium">Email</p>
                                <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <Shield class="h-4 w-4 text-muted-foreground" />
                            <div>
                                <p class="text-sm font-medium">Email Verification</p>
                                <span
                                    :class="user.email_verified_at 
                                        ? 'bg-green-100 text-green-800' 
                                        : 'bg-yellow-100 text-yellow-800'"
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{ user.email_verified_at ? 'Verified' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Account Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Account Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center gap-3">
                            <Calendar class="h-4 w-4 text-muted-foreground" />
                            <div>
                                <p class="text-sm font-medium">Member Since</p>
                                <p class="text-sm text-muted-foreground">{{ formatDate(user.created_at) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <Calendar class="h-4 w-4 text-muted-foreground" />
                            <div>
                                <p class="text-sm font-medium">Last Updated</p>
                                <p class="text-sm text-muted-foreground">{{ formatDate(user.updated_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- User ID Information -->
            <Card>
                <CardHeader>
                    <CardTitle>System Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center gap-3">
                        <div>
                            <p class="text-sm font-medium">User ID</p>
                            <p class="text-sm text-muted-foreground">{{ user.id }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppSidebarLayout>
</template> 