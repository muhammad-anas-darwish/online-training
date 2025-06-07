<?php

namespace Modules\Training\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Training\Entities\Category;

class UpdateCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique(Category::class, 'name')->ignore($this->category)],
        ];  
    }

    public function authorize(): bool
    {
        return true;
    }
}