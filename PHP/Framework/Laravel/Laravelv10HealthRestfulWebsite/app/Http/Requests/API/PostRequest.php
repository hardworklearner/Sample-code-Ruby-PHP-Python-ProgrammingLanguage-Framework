<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
        $id = $this->route('post');
        $title = $this->input('title');
        return [
            'title' => 'required',
            'description' => '',
            'post_content' => 'required',
            'category_id' =>
            [
                'required',
                'exists:categories,id',
                Rule::unique('posts')->where(function ($query) use ($title) {
                    return $query->where('title', $title);
                })->ignore($id),
            ],
            'user_id' => '',
        ];
    }
}
