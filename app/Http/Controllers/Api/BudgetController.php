<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $budgets = Budget::forUserOrGroup($user)
            ->with('category')
            ->get();

        return response()->json($budgets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $budget = Budget::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Бюджет создан', 'data' => $budget], 201);
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $budget->update($validated);

        return response()->json(['message' => 'Бюджет обновлён', 'data' => $budget]);
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();

        return response()->json(['message' => 'Бюджет удалён']);
    }
}