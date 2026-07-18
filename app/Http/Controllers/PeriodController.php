<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeriodRequest;
use App\Http\Requests\UpdatePeriodRequest;
use App\Models\Period;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    public function index(Request $request)
    {
        $periods = Period::where('user_id', auth()->id())->latest()->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $periods->items(),
                'pagination' => [
                    'current_page' => $periods->currentPage(),
                    'last_page' => $periods->lastPage(),
                    'per_page' => $periods->perPage(),
                    'total' => $periods->total(),
                ],
            ]);
        }

        return view('periods.index', compact('periods'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(StorePeriodRequest $request)
    {
        $period = Period::create($request->validated() + ['user_id' => auth()->id()]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Periode berhasil ditambahkan.',
                'data' => $period,
            ], 201);
        }

        return redirect()->route('periods.index')->with('success', 'Periode berhasil ditambahkan.');
    }

    public function show(string $id)
    {
        abort(404);
    }

    public function edit(string $id)
    {
        abort(404);
    }

    public function update(UpdatePeriodRequest $request, Period $period)
    {
        $period->update($request->validated());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Periode berhasil diperbarui.',
                'data' => $period,
            ]);
        }

        return redirect()->route('periods.index')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(Period $period, Request $request)
    {
        $period->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Periode berhasil dihapus.']);
        }

        return redirect()->route('periods.index')->with('success', 'Periode berhasil dihapus.');
    }
}
