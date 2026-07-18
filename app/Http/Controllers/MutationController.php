<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMutationRequest;
use App\Http\Requests\UpdateMutationRequest;
use App\Models\Mutation;

class MutationController extends Controller
{
    public function index()
    {
        $mutations = Mutation::latest()->paginate(10);
        return view('mutations.index', compact('mutations'));
    }

    public function create()
    {
        // simple form without modal; show create view
        return view('mutations.create');
    }


    public function store(StoreMutationRequest $request)
    {
        Mutation::create($request->validated() + ['user_id' => auth()->id()]);
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
        return redirect()->route('mutations.index')->with('success', 'Mutasi berhasil diperbarui.');
    }

    public function destroy(Mutation $mutation)
    {
        $mutation->delete();
        return redirect()->route('mutations.index')->with('success', 'Mutasi berhasil dihapus.');
    }
}
