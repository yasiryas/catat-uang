<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteFamilyMemberRequest;
use App\Models\FamilyInvitation;
use App\Models\User;
use App\Services\FamilyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class FamilyController extends Controller
{
    public function __construct(private readonly FamilyService $familyService) {}

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

    public function invite(InviteFamilyMemberRequest $request)
    {
        $result = $this->familyService->inviteMember(auth()->user(), $request->validated()['email']);

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

    public function showInvitation(Request $request, FamilyInvitation $invitation)
    {
        $token = $this->token($request);
        $result = $this->familyService->viewInvitation(auth()->user(), $invitation, $token);

        if (! $result['ok']) {
            return redirect()->route('family.index')->with('error', $result['message']);
        }

        return view('family.invitation', [
            'family' => $invitation->family,
            'invitation' => $invitation,
            'acceptUrl' => URL::temporarySignedRoute('family.invitations.accept', $invitation->expires_at, [
                'invitation' => $invitation,
                'token' => $token,
            ]),
            'rejectUrl' => URL::temporarySignedRoute('family.invitations.reject', $invitation->expires_at, [
                'invitation' => $invitation,
                'token' => $token,
            ]),
        ]);
    }

    public function acceptInvitation(Request $request, FamilyInvitation $invitation)
    {
        $result = $this->familyService->acceptInvitation(auth()->user(), $invitation, $this->token($request));

        return redirect()->route('family.index')->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function rejectInvitation(Request $request, FamilyInvitation $invitation)
    {
        $result = $this->familyService->rejectInvitation(auth()->user(), $invitation, $this->token($request));

        return redirect()->route('family.index')->with($result['ok'] ? 'success' : 'error', $result['message']);
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

    private function token(Request $request): string
    {
        return $request->string('token')->toString();
    }
}
