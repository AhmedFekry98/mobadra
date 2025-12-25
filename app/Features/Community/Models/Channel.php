<?php

namespace App\Features\Community\Models;

use App\Features\Grades\Models\Grade;
use App\Features\Groups\Models\Group;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'channelable_type',
        'channelable_id',
        'is_active',
        'is_private',
        'icon',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_private' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($channel) {
            if (empty($channel->slug)) {
                $channel->slug = Str::slug($channel->name) . '-' . Str::random(6);
            }
        });
    }

    public function channelable()
    {
        return $this->morphTo();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function activePosts()
    {
        return $this->posts()->where('is_active', true)->latest();
    }

    public function pinnedPosts()
    {
        return $this->posts()->where('is_pinned', true)->where('is_active', true);
    }

    public function group()
    {
        return $this->channelable_type === Group::class
            ? $this->channelable()
            : null;
    }

    public function grade()
    {
        return $this->channelable_type === Grade::class
            ? $this->channelable()
            : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeGeneral($query)
    {
        return $query->where('type', 'general');
    }

    public function scopeForGroup($query, int $groupId)
    {
        return $query->where('type', 'group')
            ->where('channelable_type', Group::class)
            ->where('channelable_id', $groupId);
    }

    public function scopeForGrade($query, int $gradeId)
    {
        return $query->where('type', 'grade')
            ->where('channelable_type', Grade::class)
            ->where('channelable_id', $gradeId);
    }
}
