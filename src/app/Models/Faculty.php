<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faculty extends PersonalData
{
    use HasFactory;
    protected $primaryKey = 'faculty_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function department()
    {
        return $this->hasOne(Department::class);
    }

}
