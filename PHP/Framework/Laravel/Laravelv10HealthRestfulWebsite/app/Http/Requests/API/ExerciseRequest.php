<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ExerciseRequest extends FormRequest
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
        $exercise_name = $this->exercise_name;
        $cate_id = $this->category_id;
        $id = $this->route('exercise');
        return [
            'exercise_name' => [
                'required'
            ],
            'calories_burned' => 'required',
            'duration' => 'required',
            'category_id' =>
            [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($cate_id) {
                    return $query->where('id', $cate_id);
                }),
                Rule::unique('exercises')->where(function ($query) use ($exercise_name) {
                    return $query->where('exercise_name', $exercise_name);
                })->ignore($id)
            ],
            'exercise_type' => '',
            'exercise_level' => '',
            'exercise_description' => '',
            'exercise_image' => '',
        ];
    }
}
