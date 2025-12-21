<?php

namespace App\Features\SystemManagements\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class FAQ
 * @package App\Features\SystemManagements\Models
 */
class FAQ extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'faqs';

    protected $fillable = [
        'question',
        'answer',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
    ];

    /**
     * Fields to exclude from auditing
     */
    protected $auditExcluded = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];



}
