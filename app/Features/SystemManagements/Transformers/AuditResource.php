<?php

namespace App\Features\SystemManagements\Transformers;

use App\Enums\AuditableType;
use App\Features\AuthManagement\Transformers\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AuditResource
 * @package App\Features\SystemManagements\Transformers
 */
class AuditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $auditableType = AuditableType::fromModelClass($this->auditable_type);
        $resource = $this->resource;
        return [
            'id' => $resource?->id,
            'user' => ProfileResource::make($resource?->user),

            'action' => [
                'value' => $resource?->action->value,
                'label' => $resource?->action->label(),
                'color' => $resource?->action->color(),
                'is_sensitive' => $resource?->action->isSensitive(),
            ],
            'entity' => [
                'type' => $resource?->auditable_type,
                'id' => $resource?->auditable_id,
                'label' => $auditableType?->label() ?? 'Unknown Entity',
                'feature' => $auditableType?->getFeature() ?? 'Unknown',
                'data' => $this->when($this->relationLoaded('auditable'), function () {
                    return $this->auditable ? [
                        'exists' => true,
                        'display_name' => $this->getEntityDisplayName(),
                    ] : [
                        'exists' => false,
                        'display_name' => "Deleted Entity #{$this->auditable_id}",
                    ];
                }),
            ],
            'description' => $resource?->description,
            'formatted_description' => $resource?->formatted_description,
            'changes' => [
                'has_changes' =>   $resource?->hasFieldChanges(),
                'summary' => $resource?->changes_summary,
                'new_values' => $resource?->new_values,
            ],
            'context' => [
                'ip_address' => $resource?->ip_address,
                'user_agent' => $resource?->user_agent,
                'url' => $resource?->url,
                'method' => $resource?->method,
                'batch_id' => $resource?->batch_id,
            ],
            'metadata' => $resource?->metadata,
            'tags' => $resource?->tags,
            'timestamps' => [
                'created_at' => $resource?->created_at,
                'created_at_human' => $resource?->created_at?->diffForHumans(),
                'created_at_formatted' => $resource?->created_at?->format('M j, Y g:i A'),
            ],
        ];
    }

    /**
     * Get entity display name
     */
    protected function getEntityDisplayName(): string
    {
        if (!$this->auditable) {
            return "Deleted Entity #{$this->auditable_id}";
        }

        // Try common identifier fields
        $identifierFields = ['name', 'title', 'email', 'username', 'slug'];

        foreach ($identifierFields as $field) {
            if (isset($this->auditable->$field)) {
                return $this->auditable->$field;
            }
        }

        return "#{$this->auditable_id}";
    }
}
