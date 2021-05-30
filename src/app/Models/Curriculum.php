<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $guarded = [
        'series_marks_1',
        'series_marks_2',
        'sessional_marks',
        'university_marks',
    ];

    protected $attributes = [
        'is_feedback_complete' => false
    ];

    public $timestamps = false;

    public function student(){
         return $this->belongsTo(Student::class);
    }

    public function course(){
        return $this->belongsTo(Course::class);
    }
}
