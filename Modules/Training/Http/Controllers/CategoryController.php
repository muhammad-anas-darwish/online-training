<?php

namespace Modules\Training\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Training\DTOs\CategoryDTO;
use Modules\Training\Http\Resources\CategoryResource;
use Modules\Training\Http\Requests\StoreCategoryRequest;
use Modules\Training\Http\Requests\UpdateCategoryRequest;
use Modules\Training\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(protected readonly CategoryService $categoryService)
    {
        $this->applyPermissions(
            'training-categories', 
            ['index', 'show', 'store', 'update', 'destroy']
        );
    }

    public function index()
    {    
        $categories = $this->categoryService->all();
        return $this->paginatedResponse(CategoryResource::collection($categories));
    }
    
    public function show($id)
    {
        $category = $this->categoryService->find($id);
        return $this->successResponse(CategoryResource::make($category));
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->store(CategoryDTO::fromRequest($request->validated()));
        return $this->successResponse(CategoryResource::make($category))->created('category');
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->findWithoutRelations($id);
        $category = $this->categoryService->update($category, CategoryDTO::fromRequest($request->validated()));
        return $this->successResponse(CategoryResource::make($category))->updated('category');
    }

    public function destroy($id)
    {
        $category = $this->categoryService->findWithoutRelations($id);
        $this->categoryService->destroy($category);
        return $this->successResponse()->deleted('category');
    }
}