<?php

namespace App\Exports;

use App\Models\BookingService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class PopularServicesExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['№', 'Назва послуги', 'Кількість замовлень'];
    }

    public function array(): array
    {
        $data = BookingService::query()
            ->join('services', 'booking_services.service_id', '=', 'services.id')
            ->select('services.name', DB::raw('COUNT(*) as count'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('count')
            ->get();

        $rows = [];
        foreach ($data as $i => $row) {
            $rows[] = [$i + 1, $row->name, (int) $row->count];
        }

        return $rows;
    }
}
