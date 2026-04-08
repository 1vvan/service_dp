<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\PaymentTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RevenueByMonthExport;
use App\Exports\PopularServicesExport;
use App\Exports\BookingsByStatusExport;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function revenueByMonth(Request $request): JsonResponse
    {
        $year = $request->input('year', now()->year);
        $months = $request->input('months', 12);

        $data = PaymentTransaction::query()
            ->where('payment_status', PaymentTransaction::STATUS_COMPLETED)
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $values = [];
        $monthNames = [
            1 => 'Січень', 2 => 'Лютий', 3 => 'Березень', 4 => 'Квітень',
            5 => 'Травень', 6 => 'Червень', 7 => 'Липень', 8 => 'Серпень',
            9 => 'Вересень', 10 => 'Жовтень', 11 => 'Листопад', 12 => 'Грудень'
        ];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = $monthNames[$m] . ' ' . $year;
            $values[] = (float) ($data->get($m)?->total ?? 0);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'year' => (int) $year,
        ]);
    }

    public function popularServices(): JsonResponse
    {
        $data = BookingService::query()
            ->join('services', 'booking_services.service_id', '=', 'services.id')
            ->select('services.id', 'services.name', DB::raw('COUNT(*) as count'))
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('count')
            ->get();

        return response()->json([
            'labels' => $data->pluck('name')->toArray(),
            'values' => $data->pluck('count')->map(fn ($v) => (int) $v)->toArray(),
            'data' => $data->toArray(),
        ]);
    }

    public function bookingsByStatus(): JsonResponse
    {
        $data = DB::table('bookings')
            ->join('booking_statuses', 'bookings.status_id', '=', 'booking_statuses.id')
            ->select('booking_statuses.id', 'booking_statuses.name', DB::raw('COUNT(*) as count'))
            ->groupBy('booking_statuses.id', 'booking_statuses.name')
            ->orderByDesc('count')
            ->get();

        return response()->json([
            'labels' => $data->pluck('name')->toArray(),
            'values' => $data->pluck('count')->map(fn ($v) => (int) $v)->toArray(),
            'data' => $data->toArray(),
        ]);
    }

    public function exportRevenueExcel(Request $request): BinaryFileResponse
    {
        $year = $request->input('year', now()->year);

        return Excel::download(
            new RevenueByMonthExport($year),
            "vuruchka-{$year}.xlsx",
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function exportPopularServicesExcel(): BinaryFileResponse
    {
        return Excel::download(
            new PopularServicesExport(),
            'populyarni-posluhy.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function exportBookingsByStatusExcel(): BinaryFileResponse
    {
        return Excel::download(
            new BookingsByStatusExport(),
            'zapysy-za-statusamy.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}
