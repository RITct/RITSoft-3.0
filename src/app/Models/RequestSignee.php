<?php

namespace App\Models;

use App\Enums\RequestStates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestSignee extends Model
{
    use HasFactory;

    protected $fillable = [
        "state" => RequestStates::PENDING,
        "position",
        "remark"
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function request()
    {
        return $this->belongsTo(RequestModel::class);
    }

    public static function createSignee(int $user_id, int $position, int $request_id, bool $isArray)
    {
        $signee = new RequestSignee([
            "position" => $position,
        ]);
        $signee->user_id = $user_id;
        $signee->request_id = $request_id;

        if ($isArray) {
            return $signee->toArray();
        }

        return $signee;
    }
}
