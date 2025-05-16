<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class ExportController extends Controller
{
    public function exportAnalytics(Request $request)
    {
        $user = Auth::user();
        $data = [];

        $expenses = Expense::forUserOrGroup($user)
            ->with('category')
            ->get()
            ->groupBy('category.name');

        $budgets = Budget::forUserOrGroup($user)
            ->with('category')
            ->get();

        
        $data['expenses'] = $expenses;
        $data['budgets'] = $budgets;
        $data['user'] = $user;
        $data['group'] = $user->group;

        
        $pdf = PDF::loadView('exports.analytics', $data);

        return $pdf->download('analytics.pdf');
    }
} 