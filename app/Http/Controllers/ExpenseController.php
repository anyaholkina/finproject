<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Expense::where('user_id', auth()->id());

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        return response()->json($query->with('category')->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $expense = Expense::create([
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'category_id' => $validated['category_id'],
            'date' => $validated['date'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json($expense, 201);
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            return response()->json(['message' => 'Нет доступа'], 403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $expense->update($validated);

        return response()->json($expense);
    }

    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            return response()->json(['message' => 'Нет доступа'], 403);
        }

        $expense->delete();

        return response()->json(['message' => 'Трата удалена']);
    }
}