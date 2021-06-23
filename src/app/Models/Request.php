<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        "position",
        "current_position",
        "type"
    ];

    protected $with = ["currentSignee", "signees"];

    protected $guarded = [
        "payload"
    ];

    public function signees(): HasMany
    {
        return $this->hasMany(RequestSignee::class);
    }

    public function currentSignee(): HasOne
    {
        return $this->hasOne(RequestSignee::class, "current_signee");
    }

    public function setNextSignee(): bool
    {
        if ($this->currentSignee->position == $this->signees->count()) {
            // Last Signee
            $this->currentSignee->approved = true;
            $this->currentSignee->save();
            return true;
        } else {
            $this->current_signee_id = $this->signees->where("position", $this->currentSignee->position + 1)
                ->first()->id;

            $this->save();
            return false;
        }
    }
}
