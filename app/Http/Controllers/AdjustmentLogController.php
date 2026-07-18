<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdjustmentLogRequest;
use App\Http\Requests\UpdateAdjustmentLogRequest;
use App\Models\AdjustmentLog;

class AdjustmentLogController extends Controller
{
    public function index()
    {
        $adjustments = AdjustmentLog::latest()->paginate(10);

        return view('adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        return view('adjustments.create');
    }

    public function store(StoreAdjustmentLogRequest $request)
    {
        AdjustmentLog::create($request->validated() + ['user_id' => auth()->id()]);

        return redirect()->route('adjustments.index')->with('success', 'Adjustment berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(UpdateAdjustmentLogRequest $request, AdjustmentLog $adjustment)
    {
        $adjustment->update($request->validated());

        return redirect()->route('adjustments.index')->with('success', 'Adjustment berhasil diperbarui.');
    }

    public function destroy(AdjustmentLog $adjustment)
    {
        $adjustment->delete();

        return redirect()->route('adjustments.index')->with('success', 'Adjustment berhasil dihapus.');
    }
}
