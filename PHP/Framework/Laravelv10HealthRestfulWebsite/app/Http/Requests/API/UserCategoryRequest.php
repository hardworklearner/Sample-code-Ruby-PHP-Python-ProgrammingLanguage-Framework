<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserCategoryRequest extends FormRequest
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
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
                Rule::unique('category_user')->where(function ($query) {
                    return $query->where('user_id', $this->user()->id);
                }),
            ]
        ];
    }
}
