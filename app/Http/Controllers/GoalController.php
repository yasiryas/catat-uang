<?php

namespace App\Http\Controllers;

use App\Http\Concerns\RespondsWithPagination;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Period;
use App\Services\AiService;
use App\Services\GoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GoalController extends Controller
{
    use RespondsWithPagination;

    public function __construct(
        private readonly GoalService $goalService,
        private readonly AiService $aiService,
    ) {}

    public function index(Request $request)
    {
        $userId = auth()->id();
        $user = auth()->user();

        $goals = Goal::with(['period', 'category', 'account'])
            ->forUser($user)
            ->latest()
            ->paginate(10);

        $progress = $this->goalService->progressForMany($goals->getCollection());

        $goalStats = $goals->getCollection()->map(fn (Goal $goal) => [
            'id' => $goal->id,
            'name' => $goal->name,
            'type' => $goal->type,
            'target_amount' => (float) $goal->target_amount,
            'period_id' => $goal->period_id,
            'category_id' => $goal->category_id,
            'account_id' => $goal->account_id,
            'start_date' => $goal->start_date?->format('Y-m-d'),
            'end_date' => $goal->end_date?->format('Y-m-d'),
            'is_active' => $goal->is_active,
            'period' => $goal->period?->name,
            'category' => $goal->category?->name,
            'account' => $goal->account?->name,
            'progress' => $progress[$goal->id],
        ])->values();

        $periods = Period::forUser($user)->latest('year')->latest('month')->get(['id', 'name']);
        $categories = Category::forUser(auth()->user())->latest()->get(['id', 'name']);
        $accounts = Account::forUser($user)->latest()->get(['id', 'name']);

        $summary = [
            'total' => $goalStats->count(),
            'achieved' => $goalStats->where('progress.achieved', true)->count(),
            'avg_percentage' => $goalStats->isEmpty()
                ? 0
                : (int) round($goalStats->avg(fn ($goal) => $goal['progress']['percentage'])),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $goalStats,
                'pagination' => [
                    'current_page' => $goals->currentPage(),
                    'last_page' => $goals->lastPage(),
                    'per_page' => $goals->perPage(),
                    'total' => $goals->total(),
                ],
            ]);
        }

        return view('goals.index', compact('goals', 'goalStats', 'periods', 'categories', 'accounts', 'summary'));
    }

    public function store(StoreGoalRequest $request)
    {
        $goal = Goal::create($request->validated() + [
            'user_id' => auth()->id(),
            'family_id' => auth()->user()->family_id,
        ]);

        return $this->jsonOrRedirect($request, $goal, 'Target berhasil ditambahkan.', 201);
    }

    public function update(UpdateGoalRequest $request, Goal $goal)
    {
        abort_if(! $goal->inSameScope(auth()->user()), 403);

        $goal->update($request->validated());

        return $this->jsonOrRedirect($request, $goal, 'Target berhasil diperbarui.');
    }

    public function destroy(Goal $goal, Request $request)
    {
        abort_if(! $goal->inSameScope(auth()->user()), 403);

        $goal->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Target berhasil dihapus.']);
        }

        return redirect()->route('goals.index')->with('success', 'Target berhasil dihapus.');
    }

    public function ai(Request $request)
    {
        if (! $this->aiService->enabled()) {
            return response()->json([
                'message' => 'Fitur AI belum diaktifkan. Tambahkan AI_ENABLED=true dan OPENROUTER_API_KEY di file .env.',
            ], 422);
        }

        try {
            $result = $this->aiService->recommend(auth()->user());
            $result['content'] = Str::markdown($result['content'], [
                'html_input' => 'escape',
                'allow_unsafe_links' => false,
            ]);

            return response()->json($result);
        } catch (Throwable $e) {
            Log::error('AI rekomendasi gagal: '.$e->getMessage(), ['exception' => $e]);

            return response()->json([
                'message' => 'Gagal mendapatkan rekomendasi AI. Coba lagi beberapa saat.',
            ], 500);
        }
    }

    private function jsonOrRedirect(Request $request, Goal $goal, string $message, int $status = 200)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $goal,
            ], $status);
        }

        return redirect()->route('goals.index')->with('success', $message);
    }
}
