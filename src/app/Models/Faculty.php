<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Faculty extends PersonalData
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $with = ['user'];

    public $timestamps = false;

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function isStaffAdvisor(): bool
    {
        return $this->advisor_classroom_id != null;
    }

    public function isHOD(): bool
    {
        return $this->user->hasRole(Roles::HOD);
    }

    public function isPrincipal(): bool
    {
        return $this->user->hasRole(Roles::PRINCIPAL);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function advisorClassroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, "advisor_classroom_id");
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public static function getPrincipal()
    {
        return Faculty::whereHas("user.roles", function ($q) {
            $q->where("name", Roles::PRINCIPAL);
        })->first();
    }
}
