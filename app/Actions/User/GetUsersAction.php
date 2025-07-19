<?php

namespace App\Actions\User;

use App\Actions\Action;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetUsersAction extends Action
{
    /**
     * Get paginated users with search functionality.
     */
    public function execute(...$parameters): LengthAwarePaginator
    {
        $filters = $parameters[0] ?? [];
        return User::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }
} 