<?php

namespace App\Features\Courses\Models;

use App\Features\Grades\Models\Grade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'term_id',
        'grade_id',
        'title',
        'description',
        'slug',
        'is_active',
        'final_quiz_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function finalQuiz()
    {
        return $this->belongsTo(Quiz::class, 'final_quiz_id');
    }

    // media
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course-image')
        ->useFallbackUrl(asset('/img/course-default.jpg'))
            ->singleFile();
    }
}
