<?php

namespace App\Models;

use App\Enums\RequestErrors;
use App\Enums\RequestStates;
use App\Exceptions\InvalidRequestOperation;
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

    private function checkRequestPendingOrThrow()
    {
        if ($this->state != RequestStates::PENDING) {
            throw new InvalidRequestOperation(
                "Illegal attempt to update a non pending request",
                RequestErrors::ATTEMPT_TO_UPDATE_NON_PENDING_REQUEST
            );
        }
    }

    public function setNextSignee($remark): bool
    {
        $this->checkRequestPendingOrThrow();
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
        $current_signee->save();
        $this->save();
        return $returnVal;
    }

    public function reject(string $remark = null)
    {
        $this->checkRequestPendingOrThrow();
        $this->state = RequestStates::REJECTED;
        $this->currentSignee()->remark = $remark;
        $this->currentSignee()->state = RequestStates::REJECTED;
        $this->currentSignee()->save();
        $this->save();
    }

    public function performUpdation()
    {
        if ($this->state != RequestStates::APPROVED) {
            throw new InvalidRequestOperation(
                "Illegal attempt to alter payload, before approval",
                RequestErrors::ATTEMPT_TO_ALTER_PAYLOAD_WITHOUT_APPROVAL
            );
        }
        DB::table($this->table_name)->where($this->primary_field, $this->primary_value)
            ->update(json_decode($this->payload, true));
    }

    public static function createNewRequest(
        string $type,
        Model $model,
        string $primaryVal,
        array $payload,
        array $signees,
        bool $checkSpam = true,
    ) {
        if ($checkSpam && RequestModel::isLastRequestIsPending($type, $primaryVal)) {
            return null;
        }

        $request = new RequestModel([
            "type" => $type,
            "table_name" => $model->getTable(),
            "primary_field" => $model->getKeyName(),
            "primary_value" => $primaryVal,
            "payload" => json_encode($payload)
        ]);
        $request->save();
        $signee_objs = [];
        for ($i = 0; $i < count($signees); $i++) {
            array_push(
                $signee_objs,
                RequestSignee::createSignee($signees[$i], $i + 1, $request->id, true)
            );
        }
        RequestSignee::insert($signee_objs);

        return $request->id;
    }

    public static function getRequestsFromProfile($primaryVal, $requestType = null)
    {
        $requestQuery = RequestModel::where(["primary_value" => $primaryVal]);
        if ($requestType) {
            $requestQuery->where("type", $requestType);
        }
        return $requestQuery->latest()->get();
    }

    public static function isLastRequestIsPending($requestType, $primaryVal): bool
    {
        // Get last record with same type for the given primaryVal
        $lastRecord = RequestModel::getRequestsFromProfile($primaryVal, $requestType)->first();

        return $lastRecord?->state == RequestStates::PENDING;
    }
}
