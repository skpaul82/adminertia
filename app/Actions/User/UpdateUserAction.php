<?php

namespace App\Actions\User;

use App\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateUserAction extends Action
{
    /**
     * Update an existing user.
     */
    public function execute(...$parameters): User
    {
        $user = $parameters[0];
        $data = $parameters[1];
        $this->validate($data, $user);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        return $user->fresh();
    }

    /**
     * Validate the user data.
     */
    private function validate(array $data, User $user): void
    {
        $validator = validator($data, [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
} 