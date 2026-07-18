<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Period;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected function getCategories($type)
    {
        if ($type === 'mutation' || $type === 'adjustment') {
            return Category::where('type', 'income')->orWhere('type', 'expense')->latest()->get();
        }
        return Category::where('type', $type)->latest()->get();
    }

    protected function getPeriods()
    {
        return Period::where('user_id', auth()->id())->latest()->get();
    }

    // INCOME
    public function income(Request $request)
    {
        return $this->typeIndex($request, 'income', 'Pemasukan');
    }

    public function createIncome()
    {
        return redirect()->route('transactions.income.index');
    }

    public function storeIncome(StoreTransactionRequest $request)
    {
        return $this->storeTyped($request, 'income', 'Pemasukan');
    }

    public function editIncome(Transaction $transaction)
    {
        abort_unless($transaction->type === 'income', 404);
        return redirect()->route('transactions.income.index');
    }

    public function updateIncome(UpdateTransactionRequest $request, Transaction $transaction)
    {
        return $this->updateTyped($request, $transaction, 'income', 'Pemasukan');
    }

    public function destroyIncome(Transaction $transaction, Request $request)
    {
        return $this->destroyTyped($transaction, $request, 'income', 'Pemasukan');
    }

    // EXPENSE
    public function expense(Request $request)
    {
        return $this->typeIndex($request, 'expense', 'Pengeluaran');
    }

    public function createExpense()
    {
        return redirect()->route('transactions.expense.index');
    }

    public function storeExpense(StoreTransactionRequest $request)
    {
        return $this->storeTyped($request, 'expense', 'Pengeluaran');
    }

    public function editExpense(Transaction $transaction)
    {
        abort_unless($transaction->type === 'expense', 404);
        return redirect()->route('transactions.expense.index');
    }

    public function updateExpense(UpdateTransactionRequest $request, Transaction $transaction)
    {
        return $this->updateTyped($request, $transaction, 'expense', 'Pengeluaran');
    }

    public function destroyExpense(Transaction $transaction, Request $request)
    {
        return $this->destroyTyped($transaction, $request, 'expense', 'Pengeluaran');
    }

    // MUTATION
    public function mutation(Request $request)
    {
        return $this->typeIndex($request, 'mutation', 'Mutasi');
    }

    public function createMutation()
    {
        return redirect()->route('transactions.mutation.index');
    }

    public function storeMutation(StoreTransactionRequest $request)
    {
        return $this->storeTyped($request, 'mutation', 'Mutasi');
    }

    public function editMutation(Transaction $transaction)
    {
        abort_unless($transaction->type === 'mutation', 404);
        return redirect()->route('transactions.mutation.index');
    }

    public function updateMutation(UpdateTransactionRequest $request, Transaction $transaction)
    {
        return $this->updateTyped($request, $transaction, 'mutation', 'Mutasi');
    }

    public function destroyMutation(Transaction $transaction, Request $request)
    {
        return $this->destroyTyped($transaction, $request, 'mutation', 'Mutasi');
    }

    // ADJUSTMENT
    public function adjustment(Request $request)
    {
        return $this->typeIndex($request, 'adjustment', 'Penyesuaian');
    }

    public function createAdjustment()
    {
        return redirect()->route('transactions.adjustment.index');
    }

    public function storeAdjustment(StoreTransactionRequest $request)
    {
        return $this->storeTyped($request, 'adjustment', 'Penyesuaian');
    }

    public function editAdjustment(Transaction $transaction)
    {
        abort_unless($transaction->type === 'adjustment', 404);
        return redirect()->route('transactions.adjustment.index');
    }

    public function updateAdjustment(UpdateTransactionRequest $request, Transaction $transaction)
    {
        return $this->updateTyped($request, $transaction, 'adjustment', 'Penyesuaian');
    }

    public function destroyAdjustment(Transaction $transaction, Request $request)
    {
        return $this->destroyTyped($transaction, $request, 'adjustment', 'Penyesuaian');
    }

    // SHARED METHODS
    private function typeIndex(Request $request, string $type, string $title)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->where('type', $type)
            ->latest();
        $transactions = $query->paginate(10);

        $categories = $this->getCategories($type);
        $periods = $this->getPeriods();
        $accounts = \App\Models\Account::where('user_id', auth()->id())->latest()->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ]);
        }

        return view('transactions.type', compact('transactions', 'categories', 'periods', 'accounts', 'type', 'title'));
    }

    protected function getAccounts()
    {
        return \App\Models\Account::where('user_id', auth()->id())->latest()->get();
    }

    private function storeTyped(StoreTransactionRequest $request, string $type, string $label)
    {
        $transaction = Transaction::create($request->validated() + [
            'user_id' => auth()->id(),
            'type' => $type,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => "$label berhasil ditambahkan.",
                'data' => $transaction
            ], 201);
        }

        return redirect()->route("transactions.$type.index")->with('success', "$label berhasil ditambahkan.");
    }

    private function updateTyped(UpdateTransactionRequest $request, Transaction $transaction, string $type, string $label)
    {
        abort_unless($transaction->type === $type, 404);

        $transaction->update($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => "$label berhasil diperbarui.",
                'data' => $transaction
            ]);
        }

        return redirect()->route("transactions.$type.index")->with('success', "$label berhasil diperbarui.");
    }

    private function destroyTyped(Transaction $transaction, Request $request, string $type, string $label)
    {
        abort_unless($transaction->type === $type, 404);

        $transaction->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => "$label berhasil dihapus."]);
        }

        return redirect()->route("transactions.$type.index")->with('success', "$label berhasil dihapus.");
    }
}