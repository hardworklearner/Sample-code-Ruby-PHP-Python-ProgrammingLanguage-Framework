<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Category;
use App\Http\Requests\Api\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends ApiController
{
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    public function store(CategoryRequest $request)
    {
        try {
            $category_data = $request->validated();
            $category = Category::create($category_data);
            return new CategoryResource($category);
        } catch (Exception $ex) {
            return response()->json(["message" => "Create failed: " . $ex->getMessage()], 500);
        }
    }

    public function show(Category $category)
    {

        try {
            return new CategoryResource($category);
        } catch (Exception $ex) {
            return response()->json(["message" => "Category not found: " . $ex->getMessage()], 404);
        }
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $category->update($request->validated());
            return new CategoryResource($category);
        } catch (Exception $ex) {
            return response()->json(["message" => "Update failed: " . $ex->getMessage()], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            return response()->json(["message" => "Delete successful"], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => "Delete failed: " . $ex->getMessage()], 500);
        }
    }
}
