<?php

namespace App\Features\SystemManagements\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'user_informations';

    protected $fillable = [
        'user_id',
        'phone_code',
        'phone_number',
        'date_of_birth',
        'gender',
        'nationality',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'bio',
        'social_links',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'social_links' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'gender' => Gender::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
