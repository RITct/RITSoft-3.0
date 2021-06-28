<?php

namespace App\Http\Requests;

use App\Enums\RequestStates;
use App\Models\RequestModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->auth_user = Auth::user();

        if ($this->method() == "PATCH") {
            $this->requestInDb = RequestModel::findOrFail($this->route()->parameter("request"));
            return $this->requestInDb->currentSignee()->user_id == $this->auth_user->id;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->method() == "PATCH") {
            return [
                "state" => "required|in:" . RequestStates::APPROVED . "," . RequestStates::REJECTED,
                # remark optional
            ];
        }
        return [];
    }

    /**
     * Make json data validatable
     * @return array
     */
    public function all($keys = null)
    {
        if(empty($keys)){
            return parent::json()->all();
        }
        return collect(parent::json()->all())->only($keys)->toArray();
    }
}
