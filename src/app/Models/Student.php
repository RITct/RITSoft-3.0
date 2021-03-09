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

    // public function series_marks()
    // {
    //     return $this->hasMany(Series_marks::class);
    // }

    // public function sessional_marks()
    // {
    //     return $this->hasMany(Sessional_marks::class);
    // }

    // public function university_marks()
    // {
    //     return $this->hasMany(University_marks::class);
    // }

    // public function attendance()
    // {
    //     return $this->hasMany(Attendance::class);
    // }

    // public function feedback()
    // {
    //     return $this->hasMany(Feedback::class);
    // }
}
