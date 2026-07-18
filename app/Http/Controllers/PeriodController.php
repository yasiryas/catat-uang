<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePeriodRequest;
use App\Http\Requests\UpdatePeriodRequest;
use App\Models\Period;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = Period::latest()->paginate(10);

        return view('periods.index', compact('periods'));
    }

    public function create()
    {
        // not used (modal approach)
        abort(404);
    }

    public function store(StorePeriodRequest $request)
    {
        Period::create($request->validated());

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

        return redirect()->route('periods.index')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(Period $period)
    {
        $period->delete();

        return redirect()->route('periods.index')->with('success', 'Periode berhasil dihapus.');
    }
}
