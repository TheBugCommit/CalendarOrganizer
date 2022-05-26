<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TargetsUploadRequest extends FormRequest
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
        return [
            'file'        => ['required', 'mimetypes:application/json,text/plain', 'max:10240'],
            'id'          => ['required', 'numeric']
        ];
    }
}
