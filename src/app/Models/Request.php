<?php

namespace App\Models;

use App\Enums\RequestStates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $with = ["signees"];

    protected $fillable = [
        "payload",
        "current_position" => 1,
        "type",
        "table_name",
        "primary_key",
        "status" => RequestStates::PENDING
    ];

    public function signees(): HasMany
    {
        return $this->hasMany(RequestSignee::class);
    }

    public function currentSignee()
    {
        return $this->signees->where("position", $this->current_position)->first();
    }

    public function setNextSignee(): bool
    {
        $current_signee = $this->currentSignee();
        $current_signee->state = RequestStates::APPROVED;
        $this->save();

        if ($this->current_position == $this->signees->count()) {
            // Last Signee
            $this->state = RequestStates::APPROVED;
            return true;
        } else {
            $this->current_position += 1;
            $this->save();
            return false;
        }
    }
}
