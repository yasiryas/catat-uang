<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdjustmentLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MutationController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.landing');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('categories', CategoryController::class);
    Route::resource('periods', PeriodController::class);
    Route::get('periods/{period}/report', [PeriodController::class, 'report'])->name('periods.report');
    Route::resource('accounts', AccountController::class);

    // Transactions by type - explicit routes to specific controller methods
    Route::prefix('transactions')->name('transactions.')->group(function () {
        // Income
        Route::get('income', [TransactionController::class, 'income'])->name('income.index');
        Route::get('income/create', [TransactionController::class, 'createIncome'])->name('income.create');
        Route::post('income', [TransactionController::class, 'storeIncome'])->name('income.store');
        Route::get('income/{transaction}/edit', [TransactionController::class, 'editIncome'])->name('income.edit');
        Route::put('income/{transaction}', [TransactionController::class, 'updateIncome'])->name('income.update');
        Route::delete('income/{transaction}', [TransactionController::class, 'destroyIncome'])->name('income.destroy');

        // Expense
        Route::get('expense', [TransactionController::class, 'expense'])->name('expense.index');
        Route::get('expense/create', [TransactionController::class, 'createExpense'])->name('expense.create');
        Route::post('expense', [TransactionController::class, 'storeExpense'])->name('expense.store');
        Route::get('expense/{transaction}/edit', [TransactionController::class, 'editExpense'])->name('expense.edit');
        Route::put('expense/{transaction}', [TransactionController::class, 'updateExpense'])->name('expense.update');
        Route::delete('expense/{transaction}', [TransactionController::class, 'destroyExpense'])->name('expense.destroy');

        // Mutation
        Route::get('mutation', [TransactionController::class, 'mutation'])->name('mutation.index');
        Route::get('mutation/create', [TransactionController::class, 'createMutation'])->name('mutation.create');
        Route::post('mutation', [TransactionController::class, 'storeMutation'])->name('mutation.store');
        Route::get('mutation/{transaction}/edit', [TransactionController::class, 'editMutation'])->name('mutation.edit');
        Route::put('mutation/{transaction}', [TransactionController::class, 'updateMutation'])->name('mutation.update');
        Route::delete('mutation/{transaction}', [TransactionController::class, 'destroyMutation'])->name('mutation.destroy');

        // Adjustment
        Route::get('adjustment', [TransactionController::class, 'adjustment'])->name('adjustment.index');
        Route::get('adjustment/create', [TransactionController::class, 'createAdjustment'])->name('adjustment.create');
        Route::post('adjustment', [TransactionController::class, 'storeAdjustment'])->name('adjustment.store');
        Route::get('adjustment/{transaction}/edit', [TransactionController::class, 'editAdjustment'])->name('adjustment.edit');
        Route::put('adjustment/{transaction}', [TransactionController::class, 'updateAdjustment'])->name('adjustment.update');
        Route::delete('adjustment/{transaction}', [TransactionController::class, 'destroyAdjustment'])->name('adjustment.destroy');
    });

    Route::resource('mutations', MutationController::class);
    Route::resource('adjustments', AdjustmentLogController::class);

    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';