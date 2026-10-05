<?php

declare(strict_types=1);

namespace App\Http\Requests\Need;

use Illuminate\Foundation\Http\FormRequest;

final class UnderstandNeedRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:5', 'max:10000'],
            'locale' => ['nullable', 'in:am,en'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'attachment_ids' => ['nullable', 'array', 'max:10'],
            'attachment_ids.*' => ['uuid'],
        ];
    }
}
