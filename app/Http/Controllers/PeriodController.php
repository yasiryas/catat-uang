<?php

namespace App\Http\Controllers;

use App\Exports\PeriodReportExport;
use App\Http\Concerns\RespondsWithPagination;
use App\Http\Requests\StorePeriodRequest;
use App\Http\Requests\UpdatePeriodRequest;
use App\Models\Period;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PeriodController extends Controller
{
    use RespondsWithPagination;

    public function index(Request $request)
    {
        $periods = Period::forUser(auth()->user())->latest()->paginate(10);

        if ($request->ajax() || $request->wantsJson()) {
            return $this->paginatedJson($periods);
        }

        return view('periods.index', compact('periods'));
    }

    public function store(StorePeriodRequest $request)
    {
        $period = Period::create($request->validated() + ['user_id' => auth()->id(), 'family_id' => auth()->user()->family_id]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Periode berhasil ditambahkan.',
                'data' => $period,
            ], 201);
        }

        return redirect()->route('periods.index')->with('success', 'Periode berhasil ditambahkan.');
    }

    public function update(UpdatePeriodRequest $request, Period $period)
    {
        abort_if(! $period->inSameScope(auth()->user()), 403);

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
        abort_if(! $period->inSameScope(auth()->user()), 403);

        $period->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Periode berhasil dihapus.']);
        }

        return redirect()->route('periods.index')->with('success', 'Periode berhasil dihapus.');
    }

    public function report(Period $period)
    {
        abort_if(! $period->inSameScope(auth()->user()), 403);

        $filename = 'Laporan_Periode_'.str_replace(' ', '_', $period->name).'.xlsx';

        return Excel::download(new PeriodReportExport($period), $filename);
    }
}
