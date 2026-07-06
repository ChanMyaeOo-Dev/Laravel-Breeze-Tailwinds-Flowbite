<?php

namespace App\Http\Requests;

use App\Traits\RestaurantScoped;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestaurantTableRequest extends FormRequest
{
    use RestaurantScoped;

    public function authorize(): bool
    {
        $restaurantTable = $this->route('restaurant_table');

        return self::isAdmin() || $restaurantTable->belongsToCurrentRestaurant();
    }

    public function rules(): array
    {
        $restaurantTable = $this->route('restaurant_table');

        return [
            'table_number' => ['required', 'string', 'max:50', Rule::unique('restaurant_tables')->ignore($restaurantTable->id)->where('restaurant_id', auth()->id())],
            'seating_capacity' => ['required', 'integer', 'min:1'],
            'section' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'in:available,occupied,reserved'],
        ];
    }
}
