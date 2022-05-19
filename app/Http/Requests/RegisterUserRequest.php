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
            'birth_date' => ['required', 'before:today'],
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
            'email.required' => 'Your first name is required',
            'email.max' => 'Your first name cannot be more than 255 characters',
            'email.unique' => 'email',

            'name.required' => 'Your last name is required',
            'name.max' => 'Your last name cannot be more than 255 characters',

            'surname1.required' => 'Your last 1 must be letters only',
            'surname1.max' => 'Your last 1a must be letters only',

            'surname2.required' => 'Your last 2 must be letters only',
            'surname2.max' => 'Your last 2a must be letters only',

            'birth_date.required' => 'Your date name must be letters only',
            'birth_date.before' => 'Your last date must be letters only',

            'phone.numeric' => 'Your phone number is required',
            'phone.digits' => 'Your phone number cannot be more than 255 characters',

            'password.required' => 'Your pass number cannot be more than 255 characters',
            'password.min' => 'Your pass number cannot be more than 255 characters',
            'password.max' => 'Your pass number cannot be more than 255 characters',
            'password.confirmed' => 'Your pass number cannot be more than 255 characters',

            'gender.required' => 'You must create a password',

            'nation_id.required' => 'Your password must be at least 6 characters long',
        ];
    }
}
