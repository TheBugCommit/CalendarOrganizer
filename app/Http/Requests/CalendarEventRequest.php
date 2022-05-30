<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalendarEventRequest extends FormRequest
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
            "category_id" => ['required', 'numeric','exists:categories,id'],
            "calendar_id" => ['required', 'numeric' ,'exists:calendars,id'],
            "title" => ['required', 'max:30'],
            "description" => ['required', 'max:1000'],
            "location" => ['required', 'max:300'],
            "color" => ['required', 'min:7'],
            'start' => ['required', 'date', 'date_format:Y-m-d H:i:s' ,'before:end_date'],
            'end' => ['required', 'date', 'date_format:Y-m-d H:i:s' ,'after:start_date'],
            "published" => ['required'],
        ];
    }
}
