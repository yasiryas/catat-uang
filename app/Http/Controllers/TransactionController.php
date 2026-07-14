<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Period;

class TransactionController extends Controller
{
    public function index()
    {
        $type = request()->query('type');

        $query = Transaction::query()->latest();
        if (in_array($type, ['income', 'expense', 'mutation', 'adjustment'], true)) {
            $query->where('type', $type);
        }

        $transactions = $query->paginate(10);
        $categories = Category::query()->whereIn('type', ['income', 'expense'])->get();
        $periods = Period::latest()->get();

        return view('transactions.index', compact('transactions', 'categories', 'periods', 'type'));
    }

    public function create()
    {
        $type = request()->query('type', 'income');

        $categories = Category::query()->whereIn('type', ['income', 'expense'])->get();
        $periods = Period::latest()->get();

        abort_unless(in_array($type, ['income', 'expense', 'mutation', 'adjustment'], true), 404);

        return view('transactions.create', compact('categories', 'periods', 'type'));
    }


    public function store(StoreTransactionRequest $request)
    {
        $transaction = Transaction::create($request->validated() + ['user_id' => auth()->id()]);

        return redirect()->route('transactions.index', ['type' => $transaction->type])->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        $transaction = Transaction::query()->findOrFail($id);

        $categories = Category::query()->whereIn('type', ['income', 'expense'])->get();
        $periods = Period::latest()->get();

        return view('transactions.edit', compact('transaction', 'categories', 'periods'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)

    {
        $transaction->update($request->validated());

        return redirect()->route('transactions.index', ['type' => $transaction->type])->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $type = $transaction->type;
        $transaction->delete();

        return redirect()->route('transactions.index', ['type' => $type])->with('success', 'Transaksi berhasil dihapus.');
    }
}
