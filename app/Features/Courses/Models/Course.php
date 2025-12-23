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

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
    }

    public function lessonContents()
    {
        return $this->hasManyThrough(
            LessonContent::class,
            Lesson::class,
            'chapter_id', // Foreign key on lessons table (via chapters)
            'lesson_id',  // Foreign key on lesson_contents table
            'id',         // Local key on courses table
            'id'          // Local key on lessons table
        )->join('chapters', 'lessons.chapter_id', '=', 'chapters.id')
         ->where('chapters.course_id', $this->id);
    }

    public function quizzes()
    {
        return Quiz::whereIn('id', function ($query) {
            $query->select('contentable_id')
                ->from('lesson_contents')
                ->join('lessons', 'lesson_contents.lesson_id', '=', 'lessons.id')
                ->join('chapters', 'lessons.chapter_id', '=', 'chapters.id')
                ->where('chapters.course_id', $this->id)
                ->where('lesson_contents.contentable_type', Quiz::class);
        });
    }

    public function assignments()
    {
        return Assignment::whereIn('id', function ($query) {
            $query->select('contentable_id')
                ->from('lesson_contents')
                ->join('lessons', 'lesson_contents.lesson_id', '=', 'lessons.id')
                ->join('chapters', 'lessons.chapter_id', '=', 'chapters.id')
                ->where('chapters.course_id', $this->id)
                ->where('lesson_contents.contentable_type', Assignment::class);
        });
    }

    public function materials()
    {
        return Material::whereIn('id', function ($query) {
            $query->select('contentable_id')
                ->from('lesson_contents')
                ->join('lessons', 'lesson_contents.lesson_id', '=', 'lessons.id')
                ->join('chapters', 'lessons.chapter_id', '=', 'chapters.id')
                ->where('chapters.course_id', $this->id)
                ->where('lesson_contents.contentable_type', Material::class);
        });
    }

    // media
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course-image')
        ->useFallbackUrl(asset('/img/course-default.jpg'))
            ->singleFile();
    }
}
