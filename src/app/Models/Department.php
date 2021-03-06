<?php

namespace App\Models;

use App\Enums\Roles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    public function faculties()
    {
        return $this->hasMany(Faculty::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function getHOD()
    {
        return $this->faculties->first(function ($value, $_) {
            return $value->user->hasRole(Roles::HOD);
        });
    }
}
