<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PublishChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'reason'              => ['required', 'string', 'min:5', 'max:500'],
            'expected_version'    => ['required', 'integer', 'min:1'],
            'confirmation_digest' => ['nullable', 'string', 'max:128'],
        ];
    }
}
