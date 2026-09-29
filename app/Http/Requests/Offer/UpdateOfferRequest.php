<?php

namespace App\Http\Requests\Offer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'offered_price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'proposal_message' => ['sometimes', 'string', 'min:20', 'max:10000'],
            'delivery_time_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'availability_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'additional_notes' => ['sometimes', 'nullable', 'string', 'max:10000'],
        ];
    }
}
