<?php

namespace App\Features\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'term_id',
        'title',
        'description',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    // media
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course-image')
        ->useFallbackUrl(asset('/img/course-default.jpg'))
            ->singleFile();
    }
}
