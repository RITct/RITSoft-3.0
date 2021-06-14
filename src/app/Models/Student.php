<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends PersonalData
{
    use HasFactory;

    protected $primaryKey = 'admission_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [
        'roll_no'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function absent_dates(){
        return $this->hasMany(Absentee::class);
    }

    public function classroom(){
        return $this->belongsTo(Classroom::class);
    }

    public function semester(){
        return $this->classroom->semester;
    }
    public function department(){
        return $this->classroom->department;
    }
}
