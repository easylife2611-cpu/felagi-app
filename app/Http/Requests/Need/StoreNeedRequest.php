<?php

namespace App\Http\Requests\Need;

use Illuminate\Foundation\Http\FormRequest;

class StoreNeedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:20', 'max:10000'],
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'budget_min' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'budget_max' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99', 'gte:budget_min'],
            'currency' => ['nullable', 'string', 'size:3'],
            'quantity' => ['nullable', 'numeric', 'gt:0'],
            'deadline_at' => ['nullable', 'date', 'after:now'],
            'offer_deadline_at' => ['nullable', 'date', 'after:now', 'before:deadline_at'],
            'telegram_publication_acknowledged' => ['required', 'accepted'],
        ];
    }
}
