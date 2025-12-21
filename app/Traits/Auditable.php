<?php

namespace App\Traits;

use App\Enums\AuditAction;
use App\Enums\AuditableType;
use App\Features\SystemManagements\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Trait Auditable
 * 
 * Provides automatic auditing capabilities for Eloquent models
 * Tracks create, update, delete, and restore operations
 * 
 * @package App\Traits
 */
trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->auditAction(AuditAction::CREATED, null, $model->getAuditableAttributes());
        });

        static::updated(function ($model) {
            $model->auditAction(
                AuditAction::UPDATED,
                $model->getOriginal(),
                $model->getAuditableAttributes(),
                $model->getDirty()
            );
        });

        static::deleted(function ($model) {
            $model->auditAction(AuditAction::DELETED, $model->getAuditableAttributes(), null);
        });

        // Handle soft deletes restoration
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->auditAction(AuditAction::RESTORED, null, $model->getAuditableAttributes());
            });
        }
    }

    /**
     * Get all audits for this model
     */
    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable')->latest();
    }

    /**
     * Get recent audits (last 30 days)
     */
    public function recentAudits(): MorphMany
    {
        return $this->audits()->where('created_at', '>=', now()->subDays(30));
    }

    /**
     * Get audits by action
     */
    public function auditsByAction(AuditAction $action): MorphMany
    {
        return $this->audits()->where('action', $action->value);
    }

    /**
     * Get audits by user
     */
    public function auditsByUser(int $userId): MorphMany
    {
        return $this->audits()->where('user_id', $userId);
    }

    /**
     * Create an audit record
     */
    public function auditAction(
        AuditAction $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $changedFields = null,
        ?string $description = null,
        ?array $metadata = null,
        ?array $tags = null
    ): Audit {
        // Filter sensitive fields
        $oldValues = $this->filterSensitiveFields($oldValues);
        $newValues = $this->filterSensitiveFields($newValues);

        // Only include changed fields if specified
        if ($changedFields && $action === AuditAction::UPDATED) {
            $oldValues = $oldValues ? array_intersect_key($oldValues, $changedFields) : null;
            $newValues = $newValues ? array_intersect_key($newValues, $changedFields) : null;
        }

        // Generate description if not provided
        if (!$description) {
            $description = $this->generateAuditDescription($action);
        }

        // Add model-specific metadata
        $metadata = array_merge($this->getAuditMetadata(), $metadata ?? []);

        // Add model-specific tags
        $tags = array_merge($this->getAuditTags($action), $tags ?? []);

        return Audit::createAudit([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'tags' => $tags,
        ]);
    }

    /**
     * Create a custom audit record
     */
    public function audit(
        AuditAction $action,
        ?string $description = null,
        ?array $metadata = null,
        ?array $tags = null
    ): Audit {
        return $this->auditAction($action, null, null, null, $description, $metadata, $tags);
    }

    /**
     * Get attributes that should be audited
     */
    protected function getAuditableAttributes(): array
    {
        $attributes = $this->getAttributes();

        // Remove excluded fields
        $excluded = $this->getAuditExcluded();
        foreach ($excluded as $field) {
            unset($attributes[$field]);
        }

        // Only include specified fields if defined
        $included = $this->getAuditIncluded();
        if (!empty($included)) {
            $attributes = array_intersect_key($attributes, array_flip($included));
        }

        return $attributes;
    }

    /**
     * Get fields to exclude from auditing
     */
    protected function getAuditExcluded(): array
    {
        return property_exists($this, 'auditExcluded') 
            ? $this->auditExcluded 
            : ['password', 'remember_token', 'created_at', 'updated_at'];
    }

    /**
     * Get fields to include in auditing (if empty, all fields except excluded)
     */
    protected function getAuditIncluded(): array
    {
        return property_exists($this, 'auditIncluded') ? $this->auditIncluded : [];
    }

    /**
     * Get sensitive fields that should be masked in audits
     */
    protected function getSensitiveFields(): array
    {
        return property_exists($this, 'auditSensitive') 
            ? $this->auditSensitive 
            : ['password', 'password_confirmation', 'token', 'secret', 'api_key'];
    }

    /**
     * Filter out sensitive fields from audit data
     */
    protected function filterSensitiveFields(?array $data): ?array
    {
        if (!$data) {
            return $data;
        }

        $sensitive = $this->getSensitiveFields();
        foreach ($sensitive as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[HIDDEN]';
            }
        }

        return $data;
    }

    /**
     * Generate audit description
     */
    protected function generateAuditDescription(AuditAction $action): string
    {
        $modelName = class_basename($this);
        $identifier = $this->getAuditIdentifier();
        
        return match($action) {
            AuditAction::CREATED => "Created {$modelName} {$identifier}",
            AuditAction::UPDATED => "Updated {$modelName} {$identifier}",
            AuditAction::DELETED => "Deleted {$modelName} {$identifier}",
            AuditAction::RESTORED => "Restored {$modelName} {$identifier}",
            default => "{$action->label()} {$modelName} {$identifier}",
        };
    }

    /**
     * Get model identifier for audit description
     */
    protected function getAuditIdentifier(): string
    {
        // Try common identifier fields
        $identifierFields = ['name', 'title', 'email', 'username', 'slug'];
        
        foreach ($identifierFields as $field) {
            if (isset($this->attributes[$field])) {
                return "'{$this->attributes[$field]}'";
            }
        }

        return "#{$this->getKey()}";
    }

    /**
     * Get additional metadata for audit
     */
    protected function getAuditMetadata(): array
    {
        $metadata = [];

        // Add model-specific metadata
        if (method_exists($this, 'getCustomAuditMetadata')) {
            $metadata = array_merge($metadata, $this->getCustomAuditMetadata());
        }

        // Add relationship counts if specified
        if (property_exists($this, 'auditRelationshipCounts')) {
            foreach ($this->auditRelationshipCounts as $relation) {
                if (method_exists($this, $relation)) {
                    $metadata["{$relation}_count"] = $this->$relation()->count();
                }
            }
        }

        return $metadata;
    }

    /**
     * Get tags for audit categorization
     */
    protected function getAuditTags(AuditAction $action): array
    {
        $tags = [];

        // Add model class as tag
        $tags[] = Str::snake(class_basename($this));

        // Add action category tags
        if (in_array($action->value, AuditAction::getCrudActions())) {
            $tags[] = 'crud';
        }

        if (in_array($action->value, AuditAction::getSecurityActions())) {
            $tags[] = 'security';
        }

        if (in_array($action->value, AuditAction::getBusinessActions())) {
            $tags[] = 'business';
        }

        // Add feature tag
        $auditableType = AuditableType::fromModelClass(get_class($this));
        if ($auditableType) {
            $tags[] = Str::snake($auditableType->getFeature());
        }

        // Add custom tags if method exists
        if (method_exists($this, 'getCustomAuditTags')) {
            $tags = array_merge($tags, $this->getCustomAuditTags($action));
        }

        return array_unique($tags);
    }

    /**
     * Check if model should be audited for specific action
     */
    protected function shouldAudit(AuditAction $action): bool
    {
        // Check if auditing is disabled for this model
        if (property_exists($this, 'auditingEnabled') && !$this->auditingEnabled) {
            return false;
        }

        // Check if specific action is disabled
        if (property_exists($this, 'auditExcludedActions')) {
            return !in_array($action->value, $this->auditExcludedActions);
        }

        return true;
    }

    /**
     * Get audit statistics for this model
     */
    public function getAuditStats(): array
    {
        $audits = $this->audits();

        return [
            'total_audits' => $audits->count(),
            'actions' => $audits->selectRaw('action, COUNT(*) as count')
                             ->groupBy('action')
                             ->pluck('count', 'action')
                             ->toArray(),
            'users' => $audits->selectRaw('user_id, COUNT(*) as count')
                             ->whereNotNull('user_id')
                             ->groupBy('user_id')
                             ->with('user:id,name')
                             ->get()
                             ->pluck('count', 'user.name')
                             ->toArray(),
            'first_audit' => $audits->oldest()->first()?->created_at,
            'last_audit' => $audits->latest()->first()?->created_at,
        ];
    }

    /**
     * Create batch audit for multiple models
     */
    public static function batchAudit(
        array $models,
        AuditAction $action,
        ?string $description = null,
        ?array $metadata = null,
        ?array $tags = null
    ): void {
        $batchId = (string) Str::uuid();
        $records = [];

        foreach ($models as $model) {
            if (!$model instanceof self) {
                continue;
            }

            $records[] = [
                'user_id' => Auth::id(),
                'action' => $action,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'description' => $description ?? $model->generateAuditDescription($action),
                'metadata' => array_merge($model->getAuditMetadata(), $metadata ?? []),
                'tags' => array_merge($model->getAuditTags($action), $tags ?? []),
            ];
        }

        if (!empty($records)) {
            Audit::createBatchAudit($records, $batchId);
        }
    }
}
