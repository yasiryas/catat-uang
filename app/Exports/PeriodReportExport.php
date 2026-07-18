<?php

namespace App\Exports;

use App\Models\Period;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PeriodReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected Period $period;

    public function __construct(Period $period)
    {
        $this->period = $period;
    }

    public function collection(): Collection
    {
        $transactions = Transaction::where('period_id', $this->period->id)
            ->where('user_id', $this->period->user_id)
            ->with(['category', 'account'])
            ->orderBy('date')
            ->get();

        $incomeTotal = $transactions->where('type', 'income')->sum('amount');
        $expenseTotal = $transactions->where('type', 'expense')->sum('amount');

        $rows = collect();
        $rows->push(['', '', '', '', '', '']);
        $rows->push(['Laporan Periode: ' . $this->period->name, '', '', '', '', '']);
        $rows->push([
            'Tanggal: ' . $this->period->start_date . ' s/d ' . $this->period->end_date,
            '', '', '', '', '',
        ]);
        $rows->push(['Status: ' . ($this->period->is_closed ? 'Closed' : 'Open'), '', '', '', '', '']);
        $rows->push(['', '', '', '', '', '']);
        $rows->push(['Tanggal', 'Tipe', 'Kategori', 'Dompet', 'Jumlah', 'Catatan']);

        foreach ($transactions as $tx) {
            $rows->push([
                $tx->date,
                $tx->type === 'income' ? 'Pemasukan' : ($tx->type === 'expense' ? 'Pengeluaran' : ucfirst($tx->type)),
                $tx->category?->name ?? '-',
                $tx->account?->name ?? '-',
                $tx->type === 'income' ? $tx->amount : -$tx->amount,
                $tx->note ?? '',
            ]);
        }

        $rows->push(['', '', '', '', '', '']);
        $rows->push(['Total Pemasukan', '', '', '', $incomeTotal, '']);
        $rows->push(['Total Pengeluaran', '', '', '', $expenseTotal, '']);
        $rows->push(['Saldo Akhir', '', '', '', $incomeTotal - $expenseTotal, '']);

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
