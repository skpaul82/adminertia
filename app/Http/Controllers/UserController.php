<?php

namespace App\Http\Controllers;

use App\Actions\User\CreateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\GetUsersAction;
use App\Actions\User\UpdateUserAction;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetUsersAction $getUsersAction)
    {
        $users = $getUsersAction->execute([request()->only(['search'])]);

        return Inertia::render('users/Index', [
            'users' => $users,
            'filters' => request()->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CreateUserAction $createUserAction)
    {
        try {
            $createUserAction->execute([$request->all()]);

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return Inertia::render('users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, UpdateUserAction $updateUserAction)
    {
        try {
            $updateUserAction->execute([$user, $request->all()]);

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, DeleteUserAction $deleteUserAction)
    {
        try {
            $deleteUserAction->execute([$user]);

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'You cannot delete your own account.');
        }
    }
} 