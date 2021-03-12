<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'admission_no';
    public $incrementing = false;
    protected $keyType = 'string';

    public function role()
    {
        return $this->hasOne(User::class);
    }

    // public function semester_reg()
    // {
    //     return $this->hasOne(Semester_reg::class);
    // }

    // public function subject()
    // {
    //     return $this->hasMany(Subject::class);
    // }

    // public function curriculum()
    // {
    //     return $this->hasMany(Curriculum::class);
    // }

    // public function attendance()
    // {
    //     return $this->hasMany(Attendance::class);
    // }
}
