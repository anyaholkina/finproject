<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ExportController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy']);

    Route::get('/budgets', [BudgetController::class, 'index']);
    Route::post('/budgets', [BudgetController::class, 'store']);
    Route::put('/budgets/{budget}', [BudgetController::class, 'update']);
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy']);

    Route::post('/group', [GroupController::class, 'create']);
    Route::post('/group/invite', [GroupController::class, 'inviteUser']);
    Route::post('/group/remove', [GroupController::class, 'removeUser']);
    Route::post('/group/budget', [GroupController::class, 'updateBudget']);
    Route::get('/group', [GroupController::class, 'show']);
    Route::post('/group/leave', [GroupController::class, 'leave']);

    Route::get('/group/statistics', [GroupController::class, 'statistics']);

    Route::get('/analytics/summary', [AnalyticsController::class, 'summary']);
    Route::get('/analytics/by-category', [AnalyticsController::class, 'byCategory']);
    Route::get('/analytics/by-period', [AnalyticsController::class, 'byPeriod']);

    Route::get('/notifications/budget', [NotificationController::class, 'checkBudget']);

    Route::get('/export/analytics', [ExportController::class, 'exportAnalytics']);

    Route::post('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });
});