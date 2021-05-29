<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $primaryKey = 'subject_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'type',
        'credit'
    ];

    protected function courses(){
        return $this->hasMany(Course::class);
    }

    protected function department(){
        return $this->hasOne(Department::class);
    }
}
