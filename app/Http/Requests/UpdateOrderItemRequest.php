<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'string', 'in:pending,preparing,ready,served,cancelled'],
        ];
    }
}
