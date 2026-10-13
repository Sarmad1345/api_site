<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlogRequest extends FormRequest
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
     * The image is required when adding a blog and optional when updating it.
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put');

        return [
            'id' => $isUpdate ? 'required|integer' : 'prohibited',
            'email' => ['required', 'email', Rule::unique('blogs', 'email')->ignore($this->input('id'))],
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category_id' => 'required|exists:categories,id',
            'file' => ($isUpdate ? 'nullable' : 'required') . '|image|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',
        ];
    }
}
