<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create User" />

    <AppSidebarLayout :breadcrumbs="[
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Users', href: '/users' },
        { title: 'Create User', href: '/users/create' }
    ]">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="sm" @click="$inertia.visit(route('users.index'))">
                    <ArrowLeft class="mr-2 h-4 w-4" />
                    Back to Users
                </Button>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Create User</h1>
                    <p class="text-muted-foreground">
                        Add a new user to your application
                    </p>
                </div>
            </div>

            <!-- Create User Form -->
            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>User Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                :class="{ 'border-red-500': form.errors.name }"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                :class="{ 'border-red-500': form.errors.email }"
                                required
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <Label for="password">Password</Label>
                            <Input
                                id="password"
                                v-model="form.password"
                                type="password"
                                :class="{ 'border-red-500': form.errors.password }"
                                required
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <!-- Password Confirmation -->
                        <div class="space-y-2">
                            <Label for="password_confirmation">Confirm Password</Label>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                            />
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center gap-4">
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create User' }}
                            </Button>
                            <Button 
                                type="button" 
                                variant="outline" 
                                @click="$inertia.visit(route('users.index'))"
                            >
                                Cancel
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppSidebarLayout>
</template> 