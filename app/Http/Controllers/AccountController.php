<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::where('user_id', auth()->id())->latest()->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $accounts->items(),
                'pagination' => [
                    'current_page' => $accounts->currentPage(),
                    'last_page' => $accounts->lastPage(),
                    'per_page' => $accounts->perPage(),
                    'total' => $accounts->total(),
                ],
            ]);
        }

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bank,cash,ewallet'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $account = Account::create($validated + ['user_id' => auth()->id()]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Dompet berhasil ditambahkan.',
                'data' => $account,
            ], 201);
        }

        return redirect()->route('accounts.index')->with('success', 'Dompet berhasil ditambahkan.');
    }

    public function edit(Account $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);
        abort(404);
    }

    public function update(Request $request, Account $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bank,cash,ewallet'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $account->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Dompet berhasil diperbarui.',
                'data' => $account,
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'Dompet berhasil diperbarui.');
    }

    public function destroy(Account $account, Request $request)
    {
        abort_if($account->user_id !== auth()->id(), 403);

        $account->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Dompet berhasil dihapus.']);
        }

        return redirect()->route('accounts.index')->with('success', 'Dompet berhasil dihapus.');
    }
}
