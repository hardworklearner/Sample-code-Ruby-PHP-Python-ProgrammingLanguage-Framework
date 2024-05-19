<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserExerciseRequest extends FormRequest
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
        $exercise_id = $this->input('exercise_id');
        return [
            'calories_burned' => 'required',
            'duration' => 'required',
            'exercise_id' =>
            [
                'required',
                Rule::exists('exercises', 'id')->where(function ($query) use ($exercise_id) {
                    return $query->where('id', $exercise_id);
                }),
            ],
            'exercise_time' => 'required|date',
        ];
    }
}
