<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacultyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->method() == "POST")
            return [
                "id" => "required|unique:faculties,id",
                "name" => "required",
                "phone" => "required|numeric|digits:10",
                "email" => "required|email|unique:users,email"
            ];
        if($this->method() == "PATCH")
            return [
                "phone" => "numeric|digits:10",
                "email" => "email|unique:users,email"
            ];
    }
}
