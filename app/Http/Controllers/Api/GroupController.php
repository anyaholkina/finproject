<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'owner_id' => Auth::id(),
        ]);

        $user = Auth::user();
        $user->group_id = $group->id;
        $user->role = 'admin';
        $user->save();

        return response()->json(['message' => 'Группа создана', 'group' => $group]);
    }

    public function inviteUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $authUser = Auth::user();

        if ($authUser->role !== 'admin') {
            return response()->json(['message' => 'Нет доступа'], 403);
        }

        $user->group_id = $authUser->group_id;
        $user->role = 'member';
        $user->save();

        return response()->json(['message' => 'Пользователь добавлен в группу']);
    }

    public function removeUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $authUser = Auth::user();
        $user = User::findOrFail($request->user_id);

        if ($authUser->role !== 'admin' || $user->group_id !== $authUser->group_id) {
            return response()->json(['message' => 'Нет доступа'], 403);
        }

        $user->group_id = null;
        $user->role = 'personal';
        $user->save();

        return response()->json(['message' => 'Пользователь удалён из группы']);
    }

    public function show()
    {
        $group = Auth::user()->group;

        return response()->json($group ? $group->load('users') : null);
    }

    public function updateBudget(Request $request)
    {
        $request->validate([
        'budget' => 'required|numeric|min:0',
        ]);

        $group = Auth::user()->group;

        if (!$group || Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Нет доступа'], 403);
        }

        $group->budget = $request->budget;
        $group->save();

        return response()->json(['message' => 'Бюджет группы обновлён']);
    }

    public function leave()
    {
        $user = Auth::user();
        $user->group_id = null;
        $user->role = 'personal';
        $user->save();

        return response()->json(['message' => 'Вы вышли из группы']);
    }

    public function statistics()
    {
        $user = Auth::user();
        if (!$user->group_id) {
            return response()->json(['message' => 'Вы не состоите в группе'], 403);
        }

        $group = Group::findOrFail($user->group_id);
        
        
        $totalExpenses = Expense::whereHas('user', function($query) use ($group) {
            $query->where('group_id', $group->id);
        })->sum('amount');

        
        $memberStats = User::where('group_id', $group->id)
            ->with(['expenses' => function($query) {
                $query->select('user_id', DB::raw('SUM(amount) as total'))
                    ->groupBy('user_id');
            }])
            ->get()
            ->map(function($user) {
                return [
                    'name' => $user->name,
                    'total_expenses' => $user->expenses->sum('total'),
                    'role' => $user->role
                ];
            });

        
        $categoryStats = Expense::whereHas('user', function($query) use ($group) {
            $query->where('group_id', $group->id);
        })
        ->select('category_id', DB::raw('SUM(amount) as total'))
        ->groupBy('category_id')
        ->with('category')
        ->get();

        return response()->json([
            'group_name' => $group->name,
            'total_expenses' => $totalExpenses,
            'members' => $memberStats,
            'categories' => $categoryStats
        ]);
    }
}