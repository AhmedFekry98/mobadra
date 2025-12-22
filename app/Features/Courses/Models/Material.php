<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_url',
        'file_type',
        'file_size',
        'is_downloadable',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_downloadable' => 'boolean',
    ];

    public function lessonContent()
    {
        return $this->morphOne(LessonContent::class, 'contentable');
    }
}
