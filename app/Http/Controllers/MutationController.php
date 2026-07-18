<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMutationRequest;
use App\Http\Requests\UpdateMutationRequest;
use App\Models\Mutation;
use App\Models\Period;
use App\Models\Account;
use Illuminate\Http\Request;

class MutationController extends Controller
{
    public function index(Request $request)
    {
        $mutations = Mutation::where('user_id', auth()->id())->latest()->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $mutations->items(),
                'pagination' => [
                    'current_page' => $mutations->currentPage(),
                    'last_page' => $mutations->lastPage(),
                    'per_page' => $mutations->perPage(),
                    'total' => $mutations->total(),
                ],
            ]);
        }

        return view('mutations.index', compact('mutations'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(StoreMutationRequest $request)
    {
        $mutation = Mutation::create($request->validated() + ['user_id' => auth()->id()]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Mutasi berhasil ditambahkan.',
                'data' => $mutation,
            ], 201);
        }

        return redirect()->route('mutations.index')->with('success', 'Mutasi berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(UpdateMutationRequest $request, Mutation $mutation)
    {
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
        $mutation->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Mutasi berhasil dihapus.']);
        }

        return redirect()->route('mutations.index')->with('success', 'Mutasi berhasil dihapus.');
    }
}
