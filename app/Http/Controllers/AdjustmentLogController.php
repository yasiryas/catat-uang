<?php

namespace App\Http\Controllers;

use App\Http\Concerns\RespondsWithPagination;
use App\Http\Requests\StoreAdjustmentLogRequest;
use App\Http\Requests\UpdateAdjustmentLogRequest;
use App\Models\AdjustmentLog;
use App\Models\Period;
use Illuminate\Http\Request;

class AdjustmentLogController extends Controller
{
    use RespondsWithPagination;

    public function index(Request $request)
    {
        $adjustments = AdjustmentLog::with('period')
            ->forUser(auth()->user())
            ->latest()
            ->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->paginatedJson($adjustments);
        }

        $periods = Period::forUser(auth()->user())->latest('year')->latest('month')->get(['id', 'name']);

        return view('adjustments.index', compact('adjustments', 'periods'));
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
