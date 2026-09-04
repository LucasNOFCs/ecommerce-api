<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Override;

class BaseFormRequest extends FormRequest
{
    #[Override]
    protected function prepareForValidation(): void
    {
        $allowedFields = array_keys($this->rules());
        $receivedFields = array_keys($this->all());

        $unknownFields = array_diff(
            $receivedFields,
            $allowedFields
        );

        if (! empty($unknownFields)) {
            throw ValidationException::withMessages([
                'fields' => [
                    'Unknown fields: ' . implode(', ', $unknownFields),
                ],
            ]);
        }
    }
}
