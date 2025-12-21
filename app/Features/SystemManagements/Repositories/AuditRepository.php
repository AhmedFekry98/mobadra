<?php

namespace App\Features\SystemManagements\Repositories;

use App\Features\SystemManagements\Models\Audit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditRepository
{
    public function query()
    {
        return Audit::query();
    }

    public function getAll(?bool $paginate = false): Collection|LengthAwarePaginator
    {
        $query = $this->query();

        return $paginate
            ? $query->paginate(config('paginate.count'))
            : $query->get();
    }

    public function find(string $id): ?Audit
    {
        return Audit::find($id);
    }

    public function findOrFail(string $id): Audit
    {
        return Audit::findOrFail($id);
    }

    public function delete(string $id): bool
    {
        return Audit::destroy($id);
    }

    public function getByType(string $type): Collection
    {
        return Audit::where('type', $type)->get();
    }

    public function exists(string $id): bool
    {
        return Audit::where('id', $id)->exists();
    }

    public function count(): int
    {
        return Audit::count();
    }
        /**
     * Delete old audits
     */
    public function deleteOldAudits(Carbon $before): int
    {
        return Audit::where('created_at', '<', $before)->delete();
    }
}
