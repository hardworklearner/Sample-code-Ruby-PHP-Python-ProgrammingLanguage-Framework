<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'meal_name' => 'required',
            'calories_provide' => 'required',
            'meal_time' => 'required|date',
            'category_id' => '',
            'food_time' => [
                'required',
                Rule::in(['Morning', 'Breakfast', 'Lunch', 'Dinner', 'Snack']),
            ],
            'user_id' => '',
            'description' => '',
            'picture' => '',
        ];
    }
}
