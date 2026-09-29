<?php

namespace App\Http\Requests\Need;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNeedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:5', 'max:255'],
            'description' => ['sometimes', 'string', 'min:20', 'max:10000'],
            'category_id' => ['sometimes', 'uuid', 'exists:categories,id'],
            'location_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'budget_min' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'budget_max' => ['sometimes', 'nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'quantity' => ['sometimes', 'nullable', 'numeric', 'gt:0'],
            'deadline_at' => ['sometimes', 'nullable', 'date'],
            'offer_deadline_at' => ['sometimes', 'nullable', 'date'],
            'telegram_publication_acknowledged' => ['sometimes', 'accepted'],
        ];
    }
}
