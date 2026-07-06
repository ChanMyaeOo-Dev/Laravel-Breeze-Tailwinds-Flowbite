<?php

namespace App\Http\Requests;

use App\Models\RestaurantTable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $restaurantTableIds = RestaurantTable::forRestaurant()->pluck('id')->toArray();

        return [
            'table_id' => ['required', Rule::in($restaurantTableIds)],
            'special_instructions' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
