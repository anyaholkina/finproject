<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::where('user_id', auth()->id())->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'user_id' => auth()->id(),
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category); 

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return response()->json($category);
    }
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category); 

        $category->delete();

        return response()->json(['message' => 'Категория удалена']);
    }
}