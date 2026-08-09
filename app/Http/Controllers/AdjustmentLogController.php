<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdjustmentLogRequest;
use App\Http\Requests\UpdateAdjustmentLogRequest;
use App\Models\AdjustmentLog;
use Illuminate\Http\Request;

class AdjustmentLogController extends Controller
{
    public function index(Request $request)
    {
        $adjustments = AdjustmentLog::forUser(auth()->user())->latest()->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $adjustments->items(),
                'pagination' => [
                    'current_page' => $adjustments->currentPage(),
                    'last_page' => $adjustments->lastPage(),
                    'per_page' => $adjustments->perPage(),
                    'total' => $adjustments->total(),
                ],
            ]);
        }

        return view('adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(StoreAdjustmentLogRequest $request)
    {
        $adjustment = AdjustmentLog::create($request->validated() + [
            'user_id' => auth()->id(),
            'family_id' => auth()->user()->family_id,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Adjustment berhasil ditambahkan.',
                'data' => $adjustment,
            ], 201);
        }

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
        abort_if(! $adjustment->inSameScope(auth()->user()), 403);

        $adjustment->update($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Adjustment berhasil diperbarui.',
                'data' => $adjustment,
            ]);
        }

        return redirect()->route('adjustments.index')->with('success', 'Adjustment berhasil diperbarui.');
    }

    public function destroy(AdjustmentLog $adjustment, Request $request)
    {
        abort_if(! $adjustment->inSameScope(auth()->user()), 403);

        $adjustment->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Adjustment berhasil dihapus.']);
        }

        return redirect()->route('adjustments.index')->with('success', 'Adjustment berhasil dihapus.');
    }
}
