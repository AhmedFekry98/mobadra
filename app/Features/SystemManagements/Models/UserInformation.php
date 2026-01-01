<?php

namespace App\Features\SystemManagements\Models;

use App\Enums\Gender;
use App\Features\Grades\Models\Grade;
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
        'grade_id',
        'governorate_id',
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
        'acceptance_exam'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'social_links' => 'array',
        'gender' => Gender::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
