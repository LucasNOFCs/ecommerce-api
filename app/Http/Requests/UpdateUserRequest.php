<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Override;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email'],
        ];
    }

    #[Override]
    protected function prepareForValidation()
    {
        $allowedFields = array_keys($this->rules());

        $receivedFields = array_keys($this->all());

        $unknownFields = array_diff($receivedFields, $allowedFields);

        if (! empty($unknownFields)) {
            throw ValidationException::withMessages([
                'fields' => [
                    'Unknown fields: '.implode(', ', $unknownFields),
                ],
            ]);
        }
    }
}
