<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OfficeStaff extends PersonalData
{
    use HasFactory;

    public $timestamps = false;

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
