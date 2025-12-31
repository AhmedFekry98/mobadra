<?php

namespace App\Features\SystemManagements\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'governorate_id',
        'address',
        'phone',
        'email',
        'manager_name',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
