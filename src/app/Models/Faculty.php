<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faculty extends PersonalData
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function isStaffAdvisor()
    {
        // TODO
        return true;
    }

    public function isHOD()
    {
        return User::where('id', $this->user_id)->first()->hasRole(Roles::HOD);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function advisorClassroom()
    {
        return $this->belongsTo(Classroom::class, "advisor_classroom_id");
    }
}
