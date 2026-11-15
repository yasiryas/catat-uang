<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Goal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AiService
{
    public function enabled(): bool
    {
        return config('ai.enabled') && filled(config('ai.openrouter.api_key'));
    }

    /**
     * Kirim ringkasan keuangan user ke OpenRouter dan kembalikan rekomendasi.
     *
     * @return array{content: string, model: string}
     */
    public function recommend(User $user): array
    {
        $summary = $this->buildSummary($user);

        $response = Http::connectTimeout(10)
            ->timeout(config('ai.openrouter.timeout', 60))
            ->retry(2, 500)
            ->withHeaders([
                'Authorization' => 'Bearer '.config('ai.openrouter.api_key'),
                'HTTP-Referer' => url('/'),
                'X-Title' => 'KeuanganApp',
            ])
            ->acceptJson()
            ->post(config('ai.openrouter.base_url').'/chat/completions', [
                'model' => config('ai.openrouter.model'),
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user', 'content' => $this->promptFromSummary($summary)],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1200,
            ]);

        $response->throw();

        $content = $response->json('choices.0.message.content') ?? '';

        return [
            'content' => $content,
            'model' => $response->json('model') ?? config('ai.openrouter.model'),
        ];
    }

    protected function systemPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah asisten keuangan pribadi berbahasa Indonesia untuk aplikasi pencatatan keuangan. 
Tugasmu memberikan rekomendasi yang konkret, realistis, dan bisa langsung ditindaklanjuti.
Gunakan angka dan data yang diberikan. Jawab dengan markdown ringkas (judul, poin, tanpa basa-basi).
PROMPT;
    }

    protected function buildSummary(User $user): array
    {
        $userId = $user->id;
        $period = app(PeriodService::class)->ensurePeriodExists($user);

        $income = (float) Transaction::forUser($user)
            ->where('period_id', $period->id)->where('type', 'income')->sum('amount');
        $expense = (float) Transaction::forUser($user)
            ->where('period_id', $period->id)->where('type', 'expense')->sum('amount');

        $expenseByCategory = Transaction::forUser($user)
            ->where('period_id', $period->id)->where('type', 'expense')
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->map(fn ($row) => ['name' => $row->category?->name ?? 'Tanpa kategori', 'total' => (float) $row->total])
            ->sortByDesc('total')
            ->values();

        $incomeByCategory = Transaction::forUser($user)
            ->where('period_id', $period->id)->where('type', 'income')
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->map(fn ($row) => ['name' => $row->category?->name ?? 'Tanpa kategori', 'total' => (float) $row->total])
            ->sortByDesc('total')
            ->values();

        $goalService = app(GoalService::class);

        $goals = Goal::with(['period', 'category', 'account'])
            ->forUser($user)
            ->where('is_active', true)
            ->latest()
            ->get()
            ->map(fn (Goal $goal) => array_merge(
                ['name' => $goal->name, 'type' => $goal->type],
                $goalService->progressFor($goal)
            ))
            ->values();

        $accounts = Account::forUser($user)->get();
        $saldos = Transaction::forUser($user)
            ->whereIn('account_id', $accounts->modelKeys())
            ->whereIn('type', ['income', 'expense'])
            ->selectRaw("account_id, SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as saldo")
            ->groupBy('account_id')
            ->pluck('saldo', 'account_id');

        $accountSaldos = $accounts
            ->map(fn (Account $account) => [
                'name' => $account->name,
                'type' => $account->type,
                'saldo' => (float) ($saldos[$account->id] ?? 0),
            ])
            ->values();

        return [
            'periode' => $period->name,
            'pemasukan' => $income,
            'pengeluaran' => $expense,
            'selisih' => $income - $expense,
            'pengeluaran_per_kategori' => $expenseByCategory->toArray(),
            'pemasukan_per_kategori' => $incomeByCategory->toArray(),
            'target_aktif' => $goals->toArray(),
            'saldo_dompet' => $accountSaldos->toArray(),
        ];
    }

    protected function promptFromSummary(array $s): string
    {
        $fmt = fn ($v) => 'Rp '.number_format((float) $v, 0, ',', '.');

        $lines = [];
        $lines[] = "Periode saat ini: {$s['periode']}";
        $lines[] = "Total pemasukan: {$fmt($s['pemasukan'])}";
        $lines[] = "Total pengeluaran: {$fmt($s['pengeluaran'])}";
        $lines[] = "Selisih (tabungan): {$fmt($s['selisih'])}";

        $lines[] = "\nPengeluaran per kategori (terbesar ke kecil):";
        foreach ($s['pengeluaran_per_kategori'] as $cat) {
            $share = $s['pengeluaran'] > 0 ? round(($cat['total'] / $s['pengeluaran']) * 100) : 0;
            $lines[] = "- {$cat['name']}: {$fmt($cat['total'])} ({$share}%)";
        }

        $lines[] = "\nPemasukan per kategori:";
        foreach ($s['pemasukan_per_kategori'] as $cat) {
            $lines[] = "- {$cat['name']}: {$fmt($cat['total'])}";
        }

        $lines[] = "\nSaldo dompet:";
        foreach ($s['saldo_dompet'] as $acc) {
            $lines[] = "- {$acc['name']} ({$acc['type']}): {$fmt($acc['saldo'])}";
        }

        $lines[] = "\nTarget aktif & capaiannya:";
        if (empty($s['target_aktif'])) {
            $lines[] = '- (belum ada target)';
        } else {
            foreach ($s['target_aktif'] as $goal) {
                $status = $goal['achieved'] ? 'TERCAPAI' : 'belum tercapai';
                $lines[] = "- {$goal['name']} ({$goal['type']}): target {$fmt($goal['target'])}, realisasi {$fmt($goal['actual'])} ({$goal['raw_percentage']}%), status: {$status}";
            }
        }

        $lines[] = <<<'ASK'

Berdasarkan data di atas, berikan:
1. Tiga rekomendasi paling efektif untuk MENGHEMAT pengeluaran (sebutkan kategori & estimasi nominal per bulan yang bisa dihemat).
2. Dua cara realistis untuk MENINGKATKAN pendapatan (sesuai pola pemasukan user, bukan saran generik).
3. Evaluasi singkat target & capaian: mana yang paling mendesak, dan saran menyesuaikan target bila perlu.
Gunakan bahasa Indonesia yang santun dan ringkas.
ASK;

        return implode("\n", $lines);
    }
}
