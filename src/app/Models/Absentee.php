<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absentee extends Model
{
    use HasFactory;
    protected $table = "absentees";

    protected $attributes = [
        'medical_leave' => false,
        'duty_leave' => false
    ];

    public $timestamps = false;

    public function student(){
        return $this->belongsTo(Student::class);
    }
}
