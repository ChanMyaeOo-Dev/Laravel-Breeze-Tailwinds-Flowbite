<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestaurantTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_number' => ['required', 'string', 'max:50', Rule::unique('restaurant_tables')->where(fn ($query) => $query->where('restaurant_id', auth()->id()))],
            'seating_capacity' => ['required', 'integer', 'min:1'],
            'section' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'in:available,occupied,reserved'],
        ];
    }
}
