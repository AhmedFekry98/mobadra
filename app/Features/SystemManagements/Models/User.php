<?php

namespace App\Features\SystemManagements\Models;

use App\Features\Billing\Models\Payment;
use App\Features\Subscriptions\Models\Subscription;
use App\Features\Subscriptions\Models\UserCredit;
use App\Traits\Auditable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Model implements HasMedia
{
    use HasApiTokens, Notifiable, InteractsWithMedia, Authenticatable, Auditable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * Fields to exclude from auditing
     */
    protected $auditExcluded = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Sensitive fields to mask in audits
     */
    protected $auditSensitive = [
        'password',
        'remember_token',
    ];



    public function role()
    {
        return $this->belongsTo(Role::class ,'role_id','id');
    }

    public function getRoleNameAttribute()
    {
        return $this->role->name;
    }

    /**
     * Assign role to user
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first()?->id;
        }
        $this->role_id = $role;
        $this->save();
    }

    public function allPermissions()
    {
        return $this->role()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }

    public function hasPermission($permissionName)
    {
        return $this->allPermissions()
            ->pluck('name')
            ->contains($permissionName);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('user-image')
        ->useFallbackUrl(asset('/img/user-default.svg'))
            ->singleFile();
    }

    /**
     * Get the user's information
     */
    public function userInformation()
    {
        return $this->hasOne(UserInformation::class);
    }
}
