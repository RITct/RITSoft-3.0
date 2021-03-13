<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $attributes = [
        'series_marks' => null,
        'sessional_marks' => null,
        'university_marks' => null,
        'feedback' => false
    ]

    // public function subject()
    // {
    //     return $this->hasMany(Subject::class);
    // }

    // public function student()
    // {
    //     return $this->belongsTo(Student::class);
    // }
}
