<?php

namespace App\Http\Controllers;

use App\Http\Concerns\RespondsWithPagination;
use App\Http\Requests\StoreMutationRequest;
use App\Http\Requests\UpdateMutationRequest;
use App\Models\Account;
use App\Models\Mutation;
use App\Models\Period;
use Illuminate\Http\Request;

class MutationController extends Controller
{
    use RespondsWithPagination;

    public function index(Request $request)
    {
        $mutations = Mutation::with(['period', 'fromAccount', 'toAccount'])
            ->forUser(auth()->user())
            ->latest()
            ->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->paginatedJson($mutations);
        }

        $user = auth()->user();
        $periods = Period::forUser($user)->latest('year')->latest('month')->get(['id', 'name']);
        $accounts = Account::forUser($user)->latest()->get(['id', 'name']);

        return view('mutations.index', compact('mutations', 'periods', 'accounts'));
    }

    public function store(StoreMutationRequest $request)
    {
        $mutation = Mutation::create($request->validated() + [
            'user_id' => auth()->id(),
            'family_id' => auth()->user()->family_id,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Mutasi berhasil ditambahkan.',
                'data' => $mutation,
            ], 201);
        }

        return redirect()->route('mutations.index')->with('success', 'Mutasi berhasil ditambahkan.');
    }

    public function update(UpdateMutationRequest $request, Mutation $mutation)
    {
        abort_if(! $mutation->inSameScope(auth()->user()), 403);

        $mutation->update($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Mutasi berhasil diperbarui.',
                'data' => $mutation,
            ]);
        }

        return redirect()->route('mutations.index')->with('success', 'Mutasi berhasil diperbarui.');
    }

    public function destroy(Mutation $mutation, Request $request)
    {
        abort_if(! $mutation->inSameScope(auth()->user()), 403);

        $mutation->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Mutasi berhasil dihapus.']);
        }

        return redirect()->route('mutations.index')->with('success', 'Mutasi berhasil dihapus.');
    }
}
