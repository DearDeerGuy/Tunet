<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Models\Categories;
use Illuminate\Support\Facades\Log;

class CategoriesController extends Controller
{
    public function index()
    {
        $perPage = request()->get('per_page', 10);
        return response()->json(
            Categories::paginate($perPage)
        );
    }

    public function store(CreateCategoryRequest $request)
    {
        $validated = $request->validated();

        Log::channel('admin')->alert("");
        $category = Categories::create($validated);
        return response()->json($category, 201);
    }

    public function show(Categories $category)
    {
        return response()->json($category);
    }

    public function update(CreateCategoryRequest $request, Categories $category)
    {
        $validated = $request->validated();

        $category->update($validated);
        Log::channel('admin')->alert("");
        return response()->json($category);
    }

    public function destroy(Categories $category)
    {
        $category->delete();
        Log::channel('admin')->alert("");
        return response()->json(null, 204);
    }
}
