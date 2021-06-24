<?php

namespace App\Models;

use App\Enums\RequestStates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestSignee extends Model
{
    use HasFactory;

    protected $guarded = [
        "state" => RequestStates::PENDING,
        "position"
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
