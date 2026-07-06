<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_category_id' => ['nullable', 'exists:menu_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:menus,slug,'.$this->route('menu')->id],
            'image' => ['nullable', File::image()->max(config('image.max_file_size', 5) * 1024)],
            'price' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'status' => ['boolean'],
        ];
    }
}
