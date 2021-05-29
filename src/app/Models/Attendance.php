<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = "attendance";

    protected $fillable = [
        "date",
        "hour",
    ];

    public function course(){
        return $this->belongsTo(Course::class);
    }

    public function absentees(){
        return $this->hasMany(Absentee::class);
    }
}
