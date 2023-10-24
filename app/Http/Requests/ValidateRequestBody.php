<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateRequestBody extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'toEmailAddress' => 'required|array',
            'messageSubject' => 'required|string',
            'messageBody' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'toEmailAddress.required' => 'The email address is required.',
            'toEmailAddress.email' => 'Please enter a valid email address.',
            'messageSubject.required' => 'The message subject is required.',
            'messageSubject.max' => 'The message subject cannot be more than :max characters.',
            'messageBody.required' => 'The message body is required.',
        ];
    }
}
