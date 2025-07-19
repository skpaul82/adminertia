<?php

namespace App\Actions\User;

use App\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateUserAction extends Action
{
    /**
     * Create a new user.
     */
    public function execute(...$parameters): User
    {
        $data = $parameters[0];
        $this->validate($data);

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Validate the user data.
     */
    private function validate(array $data): void
    {
        $validator = validator($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
} 