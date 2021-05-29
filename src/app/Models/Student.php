<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends PersonalData
{
    use HasFactory;

    protected $primaryKey = 'admission_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function absent_dates(){
        return $this->hasMany(Absentee::class);
    }
}
