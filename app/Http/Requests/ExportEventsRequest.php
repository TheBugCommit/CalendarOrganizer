<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ExportEventsRequest extends FormRequest
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
            'start'             => ['date_format:Y-m-d H:i:s'],
            'end'               => ['date_format:Y-m-d H:i:s', 'after:start'],
            'calendar_id'       => ['required', 'numeric', 'exists:calendars,id']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json(['message' => $validator->errors()], 400);

        throw (new ValidationException($validator, $response))
            ->errorBag($this->errorBag);
    }
}
