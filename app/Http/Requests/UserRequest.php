<?php

namespace App\Http\Requests;

use App\Rules\Uppercase;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $rules = [
            'user' => ['required', 'array'],
            'user.name' => ['required', 'string', 'min:1', 'max:60'],
            'user.country' => ['required', 'string', 'min:1', 'max:60'],
            'user.city_of_registration' => ['required', 'string', 'min:1', 'max:120'],
            'user.character' => [
                'required',
                'string',
                'min:1',
                'max:3',
                'exists:nss_currencies,character',
                new Uppercase
            ],
        ];

        return $rules;
    }
}
