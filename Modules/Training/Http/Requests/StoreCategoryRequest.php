<?php

namespace Modules\Training\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Training\Enums\CategoryTypeEnum;
use Modules\Training\Entities\Category;

class StoreCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', Rule::unique(Category::class, 'name')],
            'type' => ['required', 'string', Rule::in(CategoryTypeEnum::cases())],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}