<?php

namespace App\Features\SystemManagements\Repositories;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * Get query builder for users
     */
    public function query(): Builder
    {
        return User::query();
    }

    /**
     * Get all users
     */
    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query()->with(['role']);

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Find user by ID
     */
    public function find(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find user by ID or fail
     */
    public function findOrFail(string $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Create new user
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update user
     */
    public function update(string $id, array $data): User
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user->fresh();
    }

    /**
     * Delete user
     */
    public function delete(string $id): bool
    {
        return User::destroy($id) > 0;
    }

    /**
     * Get active users
     */
    public function getActive(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = User::where('is_active', true)
            ->with(['role'])
            ->orderBy('name', 'asc');

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Get users by role
     */
    public function getByRole(string $roleId, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = User::where('role_id', $roleId)
            ->with(['role'])
            ->orderBy('name', 'asc');

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Get verified users
     */
    public function getVerified(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = User::whereNotNull('email_verified_at')
            ->with(['role'])
            ->orderBy('name', 'asc');

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Get unverified users
     */
    public function getUnverified(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = User::whereNull('email_verified_at')
            ->with(['role'])
            ->orderBy('created_at', 'desc');

        return $paginate
            ? $query->paginate(config('paginate.count', 15))
            : $query->get();
    }

    /**
     * Assign role to user
     */
    public function assignRole(string $userId, string $roleId): User
    {
        $user = $this->findOrFail($userId);
        $user->update(['role_id' => $roleId]);
        return $user->fresh(['role']);
    }

    /**
     * Get user statistics
     */
    public function getStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
            'users_by_role' => User::selectRaw('role_id, count(*) as count')
                ->whereNotNull('role_id')
                ->groupBy('role_id')
                ->with('role:id,name')
                ->get()
                ->pluck('count', 'role.name')
                ->toArray(),
        ];
    }
}
