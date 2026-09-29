<?php

namespace App\Http\Requests\Offer;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'offered_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'currency' => ['nullable', 'string', 'size:3'],
            'proposal_message' => ['required', 'string', 'min:20', 'max:10000'],
            'delivery_time_text' => ['nullable', 'string', 'max:255'],
            'availability_text' => ['nullable', 'string', 'max:255'],
            'additional_notes' => ['nullable', 'string', 'max:10000'],
            'attachment_ids' => ['nullable', 'array', 'max:10'],
            'attachment_ids.*' => ['uuid', 'exists:attachments,id'],
        ];
    }
}
