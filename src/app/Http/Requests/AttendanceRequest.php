<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if ($this->method() == "GET") {
            // index, show, create
            return [
                "from" => ["date"],
                "to" => ["date"],
            ];
        }
        if ($this->route()->getName() == "storeAttendance") {
            // store
            return [
                "date" => ["required", "date"],
                "course_id" => ["required", "integer"],
                "hour" => ["required", "integer"],
            ];
        }
        return [];
    }
}
