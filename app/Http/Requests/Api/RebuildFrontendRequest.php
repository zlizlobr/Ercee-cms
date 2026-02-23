<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Validate payload for internal frontend rebuild dispatch endpoint.
 */
class RebuildFrontendRequest extends FormRequest
{
    /**
     * Allow authenticated internal callers to use this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define accepted rebuild payload fields.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Return standardized JSON validation error payload.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
