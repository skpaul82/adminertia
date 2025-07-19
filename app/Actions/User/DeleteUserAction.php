<?php

namespace App\Actions\User;

use App\Actions\Action;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DeleteUserAction extends Action
{
    /**
     * Delete a user.
     */
    public function execute(...$parameters): bool
    {
        $user = $parameters[0];
        $this->validate($user);

        return $user->delete();
    }

    /**
     * Validate if the user can be deleted.
     */
    private function validate(User $user): void
    {
        if ($user->id === Auth::id()) {
            throw new ValidationException(
                validator([], [])->errors()->add('user', 'You cannot delete your own account.')
            );
        }
    }
} 