<?php

namespace App\Exports;

use App\Models\PaymentTransaction;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RevenueByMonthExport implements FromArray, WithHeadings
{
    public function __construct(
        protected int $year
    ) {}

    public function headings(): array
    {
        return ['Місяць', 'Виручка (грн)'];
    }

    public function array(): array
    {
        $monthNames = [
            1 => 'Січень', 2 => 'Лютий', 3 => 'Березень', 4 => 'Квітень',
            5 => 'Травень', 6 => 'Червень', 7 => 'Липень', 8 => 'Серпень',
            9 => 'Вересень', 10 => 'Жовтень', 11 => 'Листопад', 12 => 'Грудень'
        ];

        $data = PaymentTransaction::query()
            ->where('payment_status', PaymentTransaction::STATUS_COMPLETED)
            ->whereYear('created_at', $this->year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $rows = [];
        for ($m = 1; $m <= 12; $m++) {
            $rows[] = [
                $monthNames[$m] . ' ' . $this->year,
                (float) ($data->get($m)?->total ?? 0),
            ];
        }

        return $rows;
    }
}
