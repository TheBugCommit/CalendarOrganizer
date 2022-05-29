<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    protected $redirectRoute = 'auth.signin';

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
            'email' => ['required', 'email', 'max:100', 'unique:users'],
            'name'  => ['required','max:30'],
            'surname1' => ['required', 'max:30'],
            'surname2' => ['required', 'max:30'],
            'birth_date' => ['required', 'before:-18 years'],
            'phone' => ['numeric', 'digits:9', 'nullable'],
            'password' => ['required','min:8','max:20','confirmed'],
            'gender' => ['required'],
            'nation_id' => ['required'],
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
            'surname1.required' => 'First surname it\'s required',
            'surname1.max' => 'First surname max length 30',
            'surname2.required' => 'Second surname it\'s required',
            'surname2.max' => 'Second surname max length 30',
        ];
    }
}
