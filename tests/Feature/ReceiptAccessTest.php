<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Period;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ReceiptAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_owner_can_open_signed_receipt_url(): void
    {
        [$owner, $transaction] = $this->transactionWithReceipt();

        $response = $this->actingAs($owner)
            ->get($this->receiptUrl($transaction))
            ->assertOk();

        $response->assertHeader('Cache-Control', 'no-store, private');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_other_user_cannot_open_signed_receipt_url(): void
    {
        [, $transaction] = $this->transactionWithReceipt();
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get($this->receiptUrl($transaction))
            ->assertForbidden();
    }

    public function test_tampered_signature_is_rejected(): void
    {
        [$owner, $transaction] = $this->transactionWithReceipt();

        $this->actingAs($owner)
            ->get($this->receiptUrl($transaction).'&x=1')
            ->assertForbidden();
    }

    public function test_receipt_url_is_signed_in_serialized_transaction(): void
    {
        [$owner, $transaction] = $this->transactionWithReceipt();

        $response = $this->actingAs($owner)
            ->getJson(route('transactions.expense.index'))
            ->assertOk();

        $response->assertJsonFragment(['id' => $transaction->id]);
        $response->assertSee('signature=');
        $response->assertDontSee('storage/');
    }

    private function receiptUrl(Transaction $transaction): string
    {
        return URL::temporarySignedRoute('transactions.receipt', now()->addMinutes(5), [
            'transaction' => $transaction,
        ]);
    }

    private function transactionWithReceipt(): array
    {
        $owner = User::factory()->create();
        $period = Period::create([
            'user_id' => $owner->id,
            'name' => 'November 2026',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'year' => 2026,
            'month' => 11,
        ]);
        $category = Category::create(['name' => 'Makanan', 'type' => 'expense']);
        $path = 'receipts/struk.png';
        Storage::disk('local')->put($path, 'dummy');

        $transaction = Transaction::create([
            'user_id' => $owner->id,
            'period_id' => $period->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 10000,
            'date' => '2026-11-18',
            'receipt_image' => $path,
        ]);

        return [$owner, $transaction];
    }
}
