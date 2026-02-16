<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class BookingsByStatusExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['Статус', 'Кількість'];
    }

    public function array(): array
    {
        $data = DB::table('bookings')
            ->join('booking_statuses', 'bookings.status_id', '=', 'booking_statuses.id')
            ->select('booking_statuses.name', DB::raw('COUNT(*) as count'))
            ->groupBy('booking_statuses.id', 'booking_statuses.name')
            ->orderByDesc('count')
            ->get();

        $rows = [];
        foreach ($data as $row) {
            $rows[] = [$row->name, (int) $row->count];
        }

        return $rows;
    }
}
