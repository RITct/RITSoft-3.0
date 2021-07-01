<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotoUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        if ($this->route()->getName() == "uploadPhoto.store") {
            return [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
        }
        return [];
    }
}
