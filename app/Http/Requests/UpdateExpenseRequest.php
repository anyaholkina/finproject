<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{

    public function authorize(): bool
    {
        return false;
    }
    public function rules(): array
{
    return [
        'amount' => 'sometimes|numeric|min:0',
        'category_id' => 'sometimes|exists:categories,id',
        'date' => 'sometimes|date',
        'description' => 'nullable|string|max:255',
    ];
}
}
