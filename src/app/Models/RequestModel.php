<?php

namespace App\Models;

use App\Enums\RequestStates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class RequestModel extends Model
{
    use HasFactory;

    protected $with = ["signees"];
    protected $table = "requests";

    protected $fillable = [
        "payload",
        "current_position" => 1,
        "type",
        "table_name",
        "primary_field",
        "primary_value",
        "state" => RequestStates::PENDING,
    ];

    public function signees(): HasMany
    {
        return $this->hasMany(RequestSignee::class, "request_id");
    }

    public function currentSignee()
    {
        return $this->signees->where("position", $this->current_position)->first();
    }

    public function setNextSignee($remark): bool
    {
        $current_signee = $this->currentSignee();
        $current_signee->state = RequestStates::APPROVED;
        $current_signee->remark = $remark;

        if ($this->current_position == $this->signees->count()) {
            // Last Signee
            $this->state = RequestStates::APPROVED;
            $returnVal = true;
        } else {
            $this->current_position += 1;
            $returnVal = false;
        }
        $this->save();
        return $returnVal;
    }

    public function reject(string $remark = null)
    {
        $this->state = RequestStates::REJECTED;
        $this->currentSignee()->remark = $remark;
        $this->currentSignee()->state = RequestStates::REJECTED;
        $this->save();
    }

    public function performUpdation()
    {
        DB::table($this->table_name)->where($this->primary_field, $this->primary_value)
            ->update(json_decode($this->payload, true));
    }

    public static function createNewRequest(string $type, Model $model, string $primaryVal, array $payload, array $signees)
    {
        $request = new RequestModel([
            "type" => $type,
            "table_name" => $model->getTable(),
            "primary_field" => $model->getKeyName(),
            "primary_value" => $primaryVal,
            "payload" => json_encode($payload)
        ]);
        $request->save();
        for ($i =0; $i < count($signees); $i++) {
            $signees[$i]["request_id"] = $request->id;
        }
        RequestSignee::insert($signees);
    }

    public static function isLastRequestIsPending($requestType, $primaryVal): bool
    {
        // Get last record with same type for the given primaryVal
        $lastRecord = RequestModel::where(["type" => $requestType, "primary_value" => $primaryVal])
            ->latest()->first();

        return $lastRecord?->state == RequestStates::PENDING;
    }
}
