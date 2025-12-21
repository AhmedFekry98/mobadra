<?php

namespace App\Features\SystemManagements\Models;

use App\Enums\AuditAction;
use App\Enums\AuditableType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Class Audit
 * @package App\Features\SystemManagements\Models
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $auditable_type
 * @property int $auditable_id
 * @property string|null $description
 * @property array|null $old_values
 * @property array|null $new_values
 * @property array|null $metadata
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $url
 * @property string|null $method
 * @property string|null $batch_id
 * @property array|null $tags
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * 
 * @property-read User|null $user
 * @property-read Model $auditable
 */
class Audit extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'batch_id',
        'tags',
    ];

    protected $casts = [
        'action' => AuditAction::class,
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable entity
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope: Filter by action
     */
    public function scopeAction(Builder $query, string|AuditAction $action): Builder
    {
        return $query->where('action', $action instanceof AuditAction ? $action->value : $action);
    }

    /**
     * Scope: Filter by multiple actions
     */
    public function scopeActions(Builder $query, array $actions): Builder
    {
        $actionValues = array_map(fn($action) => $action instanceof AuditAction ? $action->value : $action, $actions);
        return $query->whereIn('action', $actionValues);
    }

    /**
     * Scope: Filter by auditable type
     */
    public function scopeAuditableType(Builder $query, string|AuditableType $type): Builder
    {
        return $query->where('auditable_type', $type instanceof AuditableType ? $type->getModelClass() : $type);
    }

    /**
     * Scope: Filter by user
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope: Filter by today
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope: Filter by this week
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope: Filter by this month
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope: Filter by IP address
     */
    public function scopeByIp(Builder $query, string $ip): Builder
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope: Filter by batch
     */
    public function scopeByBatch(Builder $query, string $batchId): Builder
    {
        return $query->where('batch_id', $batchId);
    }

    /**
     * Scope: Filter by tags
     */
    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Scope: Filter by multiple tags (AND logic)
     */
    public function scopeWithAllTags(Builder $query, array $tags): Builder
    {
        foreach ($tags as $tag) {
            $query->whereJsonContains('tags', $tag);
        }
        return $query;
    }

    /**
     * Scope: Filter by multiple tags (OR logic)
     */
    public function scopeWithAnyTag(Builder $query, array $tags): Builder
    {
        return $query->where(function ($q) use ($tags) {
            foreach ($tags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }

    /**
     * Scope: Sensitive actions only
     */
    public function scopeSensitive(Builder $query): Builder
    {
        $sensitiveActions = collect(AuditAction::cases())
            ->filter(fn($action) => $action->isSensitive())
            ->pluck('value')
            ->toArray();

        return $query->whereIn('action', $sensitiveActions);
    }

    /**
     * Scope: CRUD actions only
     */
    public function scopeCrudActions(Builder $query): Builder
    {
        return $query->whereIn('action', AuditAction::getCrudActions());
    }

    /**
     * Scope: Authentication actions only
     */
    public function scopeAuthActions(Builder $query): Builder
    {
        return $query->whereIn('action', AuditAction::getAuthActions());
    }

    /**
     * Scope: Security actions only
     */
    public function scopeSecurityActions(Builder $query): Builder
    {
        return $query->whereIn('action', AuditAction::getSecurityActions());
    }

    /**
     * Scope: Business actions only
     */
    public function scopeBusinessActions(Builder $query): Builder
    {
        return $query->whereIn('action', AuditAction::getBusinessActions());
    }

    /**
     * Scope: Recent audits (last 24 hours)
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subDay());
    }

    /**
     * Scope: Order by most recent
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get formatted description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        $userName = $this->user?->name ?? 'System';
        $action = $this->action ? $this->action->label() : 'Unknown Action';
        $entityType = AuditableType::fromModelClass($this->auditable_type)?->label() ?? 'Entity';

        return "{$userName} {$action} {$entityType} #{$this->auditable_id}";
    }

    /**
     * Get changes summary
     */
    public function getChangesSummaryAttribute(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changes = [];
        foreach ($this->new_values as $field => $newValue) {
            $oldValue = $this->old_values[$field] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    /**
     * Check if audit has field changes
     */
    public function hasFieldChanges(): bool
    {
        return !empty($this->changes_summary);
    }

    /**
     * Get audit color for UI
     */
    public function getColorAttribute(): string
    {
        return $this->action ? $this->action->color() : 'gray';
    }

    /**
     * Check if audit is sensitive
     */
    public function isSensitive(): bool
    {
        return $this->action ? $this->action->isSensitive() : false;
    }

    /**
     * Get audit feature
     */
    public function getFeatureAttribute(): string
    {
        $auditableType = AuditableType::fromModelClass($this->auditable_type);
        return $auditableType?->getFeature() ?? 'Unknown';
    }

    /**
     * Get user display name
     */
    public function getUserDisplayNameAttribute(): string
    {
        return $this->user?->name ?? 'System';
    }

    /**
     * Get entity display name
     */
    public function getEntityDisplayNameAttribute(): string
    {
        $auditableType = AuditableType::fromModelClass($this->auditable_type);
        return $auditableType?->label() ?? 'Entity';
    }

    /**
     * Create audit record
     */
    public static function createAudit(array $data): self
    {
        return self::create(array_merge($data, [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ]));
    }

    /**
     * Create batch audit records
     */
    public static function createBatchAudit(array $records, string $batchId = null): void
    {
        $batchId = $batchId ?? (string) \Illuminate\Support\Str::uuid();
        
        $baseData = [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'batch_id' => $batchId,
        ];

        $audits = collect($records)->map(function ($record) use ($baseData) {
            return array_merge($record, $baseData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        self::insert($audits->toArray());
    }
}
