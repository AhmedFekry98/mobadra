<?php

namespace App\Features\Badges\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Badge extends Model implements HasMedia
{
    use HasFactory ,InteractsWithMedia ,Auditable;
 protected $table = 'badges';
    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function conditions()
    {
        return $this->hasMany(BadgeCondition::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('badge-image')
        ->useFallbackUrl(asset('/img/badge-default.png'))
            ->singleFile();
    }
}
