<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'setting_key'    => ['required', 'string', 'exists:settings,key'],
            'proposed_value' => ['required'],
        ];
    }
}
