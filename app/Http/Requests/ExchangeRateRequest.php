<?php

namespace App\Http\Requests;

use App\Rules\Uppercase;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExchangeRateRequest extends FormRequest
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
            'exchange_rates' => ['required', 'array'],
            'exchange_rates.*.character' => [
                'required',
                'string',
                'min:1',
                'max:3',
                'exists:nss_currencies,character',
                'distinct',
                new Uppercase
            ],
            'exchange_rates.*.quotation' => ['required', 'numeric', 'min:0.01', 'max:9999999999999'],
            'exchange_rates.*.on_date' => ['required', 'date_format:Y-m-d'],
        ];


        return $rules;
    }
}
