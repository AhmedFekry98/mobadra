<?php

namespace App\Features\SystemManagements\Services;

use App\Features\SystemManagements\Models\GeneralSetting;
use App\Features\SystemManagements\Repositories\GeneralSettingRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GeneralSettingService
{
    public function __construct(
        protected GeneralSettingRepository $repository
    ) {}

    /**
     * Get all settings with optional search and pagination
     */
    public function getAllSettings(?string $search = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        return $this->repository->getAll($search, $paginate);
    }

    /**
     * Get setting by ID
     */
    public function getSettingById(string $id): ?GeneralSetting
    {
        return $this->repository->find($id);
    }

    /**
     * Get setting by ID or fail
     */
    public function getSettingByIdOrFail(string $id): GeneralSetting
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Get setting by key
     */
    public function getSettingByKey(string $key): ?GeneralSetting
    {
        return $this->repository->findByKey($key);
    }

    /**
     * Get setting by key or fail
     */
    public function getSettingByKeyOrFail(string $key): GeneralSetting
    {
        return $this->repository->findByKeyOrFail($key);
    }

    /**
     * Create new setting
     */
    public function createSetting(array $data): GeneralSetting
    {
        // Check if key already exists
        if ($this->repository->keyExists($data['key'])) {
            throw new \InvalidArgumentException('Setting with this key already exists');
        }

        return $this->repository->create($data);
    }

    /**
     * Update setting by ID
     */
    public function updateSettingById(string $id, array $data): GeneralSetting
    {
        $setting = $this->repository->findOrFail($id);

        // Check if key already exists (excluding current setting)
        if (isset($data['key']) && $this->repository->keyExists($data['key'], $id)) {
            throw new \InvalidArgumentException('Setting with this key already exists');
        }

        return $this->repository->update($setting, $data);
    }

    /**
     * Delete setting by ID
     */
    public function deleteSettingById(string $id): GeneralSetting
    {
        $setting = $this->repository->findOrFail($id);
        $this->repository->delete($setting);
        
        return $setting;
    }

    /**
     * Update or create setting by key
     */
    public function updateOrCreateSetting(string $key, string $value): GeneralSetting
    {
        return $this->repository->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Check if setting exists
     */
    public function settingExists(string $id): bool
    {
        return $this->repository->exists($id);
    }

    /**
     * Get total settings count
     */
    public function getTotalCount(): int
    {
        return $this->repository->count();
    }
}
