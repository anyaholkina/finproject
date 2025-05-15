<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Название категории обязательно для заполнения',
            'name.string' => 'Название категории должно быть текстом',
            'name.max' => 'Название категории не может быть длиннее 255 символов',
        ];
    }
} 