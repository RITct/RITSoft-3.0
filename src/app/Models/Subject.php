<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'credit'
    ];

    protected function courses()
    {
        return $this->hasMany(Course::class);
    }

    protected function department()
    {
        return $this->hasOne(Department::class);
    }
}
