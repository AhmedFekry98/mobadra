<?php

namespace App\Features\SystemManagements\Repositories;

use App\Features\SystemManagements\Models\GeneralSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class GeneralSettingRepository
{
    public function query()
    {
        return GeneralSetting::query();
    }

    public function getAll(?string $search = null, ?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = GeneralSetting::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('value', 'like', "%{$search}%");
            });
        }

        return $paginate ? $query->paginate(config('paginate.count')) : $query->get();
    }

    public function find(string $id): ?GeneralSetting
    {
        return GeneralSetting::find($id);
    }

    public function findOrFail(string $id): GeneralSetting
    {
        return GeneralSetting::findOrFail($id);
    }

    public function findByKey(string $key): ?GeneralSetting
    {
        return GeneralSetting::where('key', $key)->first();
    }

    public function findByKeyOrFail(string $key): GeneralSetting
    {
        return GeneralSetting::where('key', $key)->firstOrFail();
    }

    public function create(array $data): GeneralSetting
    {
        return GeneralSetting::create($data);
    }

    public function update(GeneralSetting $setting, array $data): GeneralSetting
    {
        $setting->update($data);
        return $setting->fresh();
    }

    public function delete(GeneralSetting $setting): bool
    {
        return $setting->delete();
    }

    public function updateOrCreate(array $attributes, array $values): GeneralSetting
    {
        return GeneralSetting::updateOrCreate($attributes, $values);
    }

    public function keyExists(string $key, ?string $excludeId = null): bool
    {
        $query = GeneralSetting::where('key', $key);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    public function exists(string $id): bool
    {
        return GeneralSetting::where('id', $id)->exists();
    }

    public function count(): int
    {
        return GeneralSetting::count();
    }
}
