<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailSendRequest extends FormRequest
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
            'view'          => ['required'],
            'subject'       => ['required'],
            'body'          => ['required'],
            'recipients'    => ['required','array','min:1'],
            'cc'            => ['array', 'nullable'],
            'bcc'           => ['array', 'nullable'],
            'attachments'   => ['array', 'nullable'],
            'replys'        => ['array', 'nullable'],
            'view_data'     => ['nullable']
        ];
    }
}
