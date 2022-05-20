<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalendarStoreRequest extends FormRequest
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
            'title' => ['required', 'min:2', 'max:200'],
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date' => ['required', 'date', 'after:start_date']
        ];
    }

    /**
     * Assign error messages for rules
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required'      => 'Email is required',
            'title.min'           => 'Title length, must be greater than 2',
            'start_date.required' => 'Start date it\'s required',
            'start_date.date'     => 'The start date is not a valid date',
            'start_date.before'   => 'The start date cannot exceed the end date',
            'end_date.required'   => 'End date it\'s required',
            'end_date.date'       => 'The end date is not a valid date',
            'end_date.before'     => 'The end date cannot be less than the start date',
        ];
    }
}
