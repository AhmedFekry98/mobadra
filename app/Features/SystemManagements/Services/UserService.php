<?php

namespace App\Features\SystemManagements\Services;

use App\Features\SystemManagements\Models\User;
use App\Features\SystemManagements\Repositories\UserRepository;
use App\Features\SystemManagements\Metadata\UserMetadata;
use App\Traits\HasGlobalQueryHandlers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    use HasGlobalQueryHandlers;

    public function __construct(
        protected UserRepository $repository
    ) {}

    /**
     * Get All Users with global query handlers
     */
    public function getUsers(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query();

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => UserMetadata::getSearchableColumns(),
            'filters' => UserMetadata::getFilters(),
            'operators' => UserMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get Staff Users (excluding certain roles)
     */
    public function getStaffUsers(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query()->whereHas('role', function ($q) {
            $q->whereNotIn('name', config('staff.excluded_roles', ['customer', 'guest']));
        });

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => UserMetadata::getSearchableColumns(),
            'filters' => UserMetadata::getFilters(),
            'operators' => UserMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get sortable columns from metadata
     */
    protected function getSortableColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'role_id',
            'is_active',
            'email_verified_at',
            'created_at',
            'updated_at'
        ];
    }

    /**
     * Get user by ID
     */
    public function getUserById(string $id): User
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Get staff user by ID (with role filtering)
     */
    public function getStaffUserById(string $id): User
    {
        return User::whereHas('role', function ($q) {
            $q->whereNotIn('name', config('staff.excluded_roles', ['customer', 'guest']));
        })->findOrFail($id);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): User
    {
        return $this->repository->create($data);
    }

    /**
     * Update user
     */
    public function updateUser(string $id, array $data): User
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Update staff user (with role filtering)
     */
    public function updateStaffUser(string $id, array $data): User
    {
        $user = $this->getStaffUserById($id);
        $user->update($data);
        return $user->fresh(['role']);
    }

    /**
     * Delete user
     */
    public function deleteUser(string $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Delete staff user (with role filtering)
     */
    public function deleteStaffUser(string $id): bool
    {
        $user = $this->getStaffUserById($id);
        return $user->delete();
    }

    /**
     * Get Student Users (only student role)
     */
    public function getStudentUsers(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query()->whereHas('role', function ($q) {
            $q->where('name', 'student');
        });

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => UserMetadata::getSearchableColumns(),
            'filters' => UserMetadata::getFilters(),
            'operators' => UserMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get student user by ID (with role filtering)
     */
    public function getStudentUserById(string $id): User
    {
        return User::whereHas('role', function ($q) {
            $q->where('name', 'student');
        })->findOrFail($id);
    }

    /**
     * Update student user (with role filtering)
     */
    public function updateStudentUser(string $id, array $data): User
    {
        $user = $this->getStudentUserById($id);
        $user->update($data);
        return $user->fresh(['role']);
    }

    /**
     * Delete student user (with role filtering)
     */
    public function deleteStudentUser(string $id): bool
    {
        $user = $this->getStudentUserById($id);
        return $user->delete();
    }

    /**
     * Get Teacher Users (only teacher role)
     */
    public function getTeacherUsers(?string $search = null, ?array $filter = null, ?array $sort = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->repository->query()->whereHas('role', function ($q) {
            $q->where('name', 'teacher');
        });

        $data = [
            'search' => $search,
            'filter' => $filter,
            'sort' => $sort,
        ];

        $config = [
            'searchable_columns' => UserMetadata::getSearchableColumns(),
            'filters' => UserMetadata::getFilters(),
            'operators' => UserMetadata::getOperators(),
            'sortable_columns' => $this->getSortableColumns(),
        ];

        return $this->getWithGlobalHandlers($query, $data, $config, $paginate);
    }

    /**
     * Get teacher user by ID (with role filtering)
     */
    public function getTeacherUserById(string $id): User
    {
        return User::whereHas('role', function ($q) {
            $q->where('name', 'teacher');
        })->findOrFail($id);
    }

    /**
     * Update teacher user (with role filtering)
     */
    public function updateTeacherUser(string $id, array $data): User
    {
        $user = $this->getTeacherUserById($id);
        $user->update($data);
        return $user->fresh(['role']);
    }

    /**
     * Delete teacher user (with role filtering)
     */
    public function deleteTeacherUser(string $id): bool
    {
        $user = $this->getTeacherUserById($id);
        return $user->delete();
    }

    /**
     * Get active users
     */
    public function getActiveUsers(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getActive($paginate);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $roleId, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getByRole($roleId, $paginate);
    }

    /**
     * Get verified users
     */
    public function getVerifiedUsers(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getVerified($paginate);
    }

    /**
     * Get unverified users
     */
    public function getUnverifiedUsers(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getUnverified($paginate);
    }

    /**
     * Assign role to user
     */
    public function assignRole(string $userId, string $roleId): User
    {
        return $this->repository->assignRole($userId, $roleId);
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        return $this->repository->getStats();
    }
}
