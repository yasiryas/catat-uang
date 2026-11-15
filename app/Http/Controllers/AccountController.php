<?php

namespace App\Http\Controllers;

use App\Http\Concerns\RespondsWithPagination;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    use RespondsWithPagination;

    public function index(Request $request)
    {
        $accounts = Account::forUser(auth()->user())->latest()->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->paginatedJson($accounts);
        }

        return view('accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bank,cash,ewallet'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $account = Account::create($validated + ['user_id' => auth()->id(), 'family_id' => auth()->user()->family_id]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Dompet berhasil ditambahkan.',
                'data' => $account,
            ], 201);
        }

        return redirect()->route('accounts.index')->with('success', 'Dompet berhasil ditambahkan.');
    }

    public function update(Request $request, Account $account)
    {
        abort_if(! $account->inSameScope(auth()->user()), 403);

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
        abort_if(! $account->inSameScope(auth()->user()), 403);

        $account->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Dompet berhasil dihapus.']);
        }

        return redirect()->route('accounts.index')->with('success', 'Dompet berhasil dihapus.');
    }
}
