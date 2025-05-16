<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function checkBudget()
    {
        $user = Auth::user();
        $notifications = [];

        
        $personalBudgets = Budget::where('user_id', $user->id)->get();
        foreach ($personalBudgets as $budget) {
            $expenses = Expense::where('user_id', $user->id)
                ->where('category_id', $budget->category_id)
                ->whereBetween('date', [$budget->start_date, $budget->end_date])
                ->sum('amount');

            if ($expenses > $budget->amount) {
                $notifications[] = [
                    'type' => 'budget_exceeded',
                    'message' => "Превышен личный бюджет в категории {$budget->category->name}",
                    'amount' => $expenses - $budget->amount
                ];
            }
        }

        
        if ($user->group_id) {
            $group = Group::find($user->group_id);
            if ($group && $group->budget) {
                $groupExpenses = Expense::whereHas('user', function($query) use ($group) {
                    $query->where('group_id', $group->id);
                })->sum('amount');

                if ($groupExpenses > $group->budget) {
                    $notifications[] = [
                        'type' => 'group_budget_exceeded',
                        'message' => "Превышен общий бюджет группы {$group->name}",
                        'amount' => $groupExpenses - $group->budget
                    ];
                }
            }
        }

        return response()->json($notifications);
    }
} 