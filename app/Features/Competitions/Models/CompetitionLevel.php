<?php

namespace App\Features\Competitions\Models;

use App\Features\Courses\Models\Course;
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
        'course_slug',
    ];

    protected $casts = [
        'level_order' => 'integer',
        'capacity' => 'integer',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function course()
    {
        return Course::where('slug', $this->course_slug)->first();
    }
}
