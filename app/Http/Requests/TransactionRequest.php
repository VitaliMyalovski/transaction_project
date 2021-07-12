<?php

namespace App\Http\Requests;

use App\Rules\Uppercase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
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
        if (strpos($this->getUri(), '/refill')) {
            //в случае пополнение
            $rules = [
                'user' => ['required', 'array'],
                'user.name' => ['required', 'string', 'min:1', 'max:60'],
                'user.country' => ['required', 'string', 'min:1', 'max:60'],
                'user.city_of_registration' => ['required', 'string', 'min:1', 'max:120'],
                'value' => ['required', 'numeric', 'min:0.01', 'max:9999999999999']
            ];
        } elseif (strpos($this->getUri(), '/transfer')) {
            //в случае перевода
            $rules = [
                'from_user' => ['required', 'array'],
                'from_user.name' => ['required', 'string', 'min:1', 'max:60'],
                'from_user.country' => ['required', 'string', 'min:1', 'max:60'],
                'from_user.city_of_registration' => ['required', 'string', 'min:1', 'max:120'],
                'to_user' => ['required', 'array'],
                'to_user.name' => ['required', 'string', 'min:1', 'max:60'],
                'to_user.country' => ['required', 'string', 'min:1', 'max:60'],
                'to_user.city_of_registration' => ['required', 'string', 'min:1', 'max:120'],
                'value' => ['required', 'numeric', 'min:0.01', 'max:9999999999999'],
                'whose_currency' => ['required', 'string', Rule::in(['from', 'to']),]
            ];
        }
        return $rules;
    }
}
