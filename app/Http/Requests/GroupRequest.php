<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|exists:users,email',
            'user_id' => 'required|exists:users,id',
            'budget' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Название группы обязательно для заполнения',
            'name.string' => 'Название группы должно быть текстом',
            'name.max' => 'Название группы не может быть длиннее 255 символов',
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Неверный формат email',
            'email.exists' => 'Пользователь с таким email не найден',
            'user_id.required' => 'ID пользователя обязателен',
            'user_id.exists' => 'Пользователь не найден',
            'budget.required' => 'Бюджет обязателен для заполнения',
            'budget.numeric' => 'Бюджет должен быть числом',
            'budget.min' => 'Бюджет не может быть отрицательным',
        ];
    }
} 