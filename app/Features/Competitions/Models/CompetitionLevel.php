<?php

namespace App\Features\Competitions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'name',
        'description',
        'level_order',
        'capacity',
    ];

    protected $casts = [
        'level_order' => 'integer',
        'capacity' => 'integer',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
