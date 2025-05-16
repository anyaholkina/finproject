<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function summary(Request $request)
    {
        $user = Auth::user();

        $expenses = Expense::forUserOrGroup($user)->get();

        $total = $expenses->sum('amount');

        return response()->json(['total_expenses' => $total]);
    }

    public function byCategory(Request $request)
    {
        $user = Auth::user();

        $data = Expense::forUserOrGroup($user)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->with('category')
            ->get();

        return response()->json($data);
    }

    public function byPeriod(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
        ]);

        $user = Auth::user();

        $data = Expense::forUserOrGroup($user)
            ->whereBetween('date', [$request->start, $request->end])
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }
}
