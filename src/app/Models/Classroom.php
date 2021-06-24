<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = [
        "semester",
        "degree_type"
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    public function staffAdvisors()
    {
        return $this->hasMany(Faculty::class, "advisor_classroom_id");
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function promotion()
    {
        return $this->hasOne(Classroom::class, "promotion_id");
    }
}
