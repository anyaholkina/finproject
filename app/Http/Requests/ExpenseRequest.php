<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Сумма обязательна для заполнения',
            'amount.numeric' => 'Сумма должна быть числом',
            'amount.min' => 'Сумма не может быть отрицательной',
            'category_id.required' => 'Категория обязательна для заполнения',
            'category_id.exists' => 'Выбранная категория не существует',
            'date.required' => 'Дата обязательна для заполнения',
            'date.date' => 'Неверный формат даты',
            'description.max' => 'Описание не может быть длиннее 1000 символов',
        ];
    }
} 