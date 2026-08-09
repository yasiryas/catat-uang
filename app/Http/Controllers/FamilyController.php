<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FamilyService;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function __construct(private readonly FamilyService $familyService)
    {
    }

    public function index()
    {
        return view('family.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        if (auth()->user()->belongsToFamily()) {
            return back()->with('error', 'Anda sudah berada dalam keluarga.');
        }

        $this->familyService->create(auth()->user(), $request->name);

        return redirect()->route('family.index')->with('success', 'Keluarga berhasil dibuat. Undang pasangan/anak untuk bergabung.');
    }

    public function invite(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email'],
        ]);

        $result = $this->familyService->inviteMember(auth()->user(), $request->email);

        if (! $result['ok']) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $result['message']], 422);
            }

            return back()->with('error', $result['message']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }

    public function remove(Request $request, User $member)
    {
        $result = $this->familyService->removeMember(auth()->user(), $member);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result['ok']
                ? ['message' => $result['message']]
                : ['message' => $result['message']], $result['ok'] ? 200 : 422);
        }

        return $result['ok']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function leave(Request $request)
    {
        $result = $this->familyService->leave(auth()->user());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result['ok']
                ? ['message' => $result['message']]
                : ['message' => $result['message']], $result['ok'] ? 200 : 422);
        }

        return $result['ok']
            ? redirect()->route('family.index')->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function destroy(Request $request)
    {
        $this->familyService->destroy(auth()->user());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Keluarga dibubarkan.']);
        }

        return redirect()->route('family.index')->with('success', 'Keluarga dibubarkan.');
    }
}