<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => [
                'required',
                'string',
                'max:100',
            ],

            'recipient_name' => [
                'required',
                'string',
                'max:150',
            ],

            'street' => [
                'required',
                'string',
                'max:255',
            ],

            'number' => [
                'required',
                'string',
                'max:20',
            ],

            'complement' => [
                'nullable',
                'string',
                'max:255',
            ],

            'neighborhood' => [
                'required',
                'string',
                'max:150',
            ],

            'city' => [
                'required',
                'string',
                'max:150',
            ],

            'state' => [
                'required',
                'string',
                'max:100',
            ],

            'postal_code' => [
                'required',
                'string',
                'max:20',
            ],

            'country' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }
}
