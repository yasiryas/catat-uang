<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Period;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        $query = Transaction::query()->latest();
        if (in_array($type, ['income', 'expense', 'mutation', 'adjustment'], true)) {
            $query->where('type', $type);
        }

        $transactions = $query->paginate(10);
        $categories = Category::query()->whereIn('type', ['income', 'expense'])->get();
        $periods = Period::latest()->get();

        if ($request->ajax() || $request->query('ajax')) {
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

        return view('transactions.index', compact('transactions', 'categories', 'periods', 'type'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = Transaction::create($request->validated() + ['user_id' => auth()->id()]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Transaksi berhasil ditambahkan.',
                'data' => $transaction,
            ], 201);
        }

        return redirect()->route('transactions.index', ['type' => $transaction->type])->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Transaksi berhasil diperbarui.',
                'data' => $transaction,
            ]);
        }

        return redirect()->route('transactions.index', ['type' => $transaction->type])->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction, Request $request)
    {
        $type = $transaction->type;
        $transaction->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Transaksi berhasil dihapus.',
            ]);
        }

        return redirect()->route('transactions.index', ['type' => $type])->with('success', 'Transaksi berhasil dihapus.');
    }
}
