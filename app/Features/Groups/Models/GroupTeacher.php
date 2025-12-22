<?php

namespace App\Features\Groups\Models;

use App\Features\SystemManagements\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'teacher_id',
        'assigned_at',
        'is_primary',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'is_primary' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
