<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'Категория обязательна для заполнения',
            'category_id.exists' => 'Выбранная категория не существует',
            'amount.required' => 'Сумма обязательна для заполнения',
            'amount.numeric' => 'Сумма должна быть числом',
            'amount.min' => 'Сумма не может быть отрицательной',
            'month.required' => 'Месяц обязателен для заполнения',
            'month.integer' => 'Месяц должен быть числом',
            'month.min' => 'Месяц должен быть от 1 до 12',
            'month.max' => 'Месяц должен быть от 1 до 12',
            'year.required' => 'Год обязателен для заполнения',
            'year.integer' => 'Год должен быть числом',
            'year.min' => 'Год должен быть не раньше 2000',
            'year.max' => 'Год должен быть не позже 2100',
        ];
    }
} 