<?php

namespace App\Livewire\Admin;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class DashboardStats extends Component
{
    public function exportRevenueCsv()
    {
        $series = $this->revenueSeries(12);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="revenue-trend-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($series) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['month', 'bookings', 'revenue_usd']);

            foreach ($series as $row) {
                fputcsv($handle, [
                    $row['month_key'],
                    $row['bookings'],
                    number_format($row['revenue'], 2, '.', ''),
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, 'revenue-trend-' . now()->format('Y-m-d') . '.csv', $headers);
    }

    private function revenueSeries(int $months = 12): array
    {
        $series = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->startOfMonth()->subMonths($i);

            $monthlyQuery = Booking::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);

            $bookings = $monthlyQuery->count();
            $revenue = $monthlyQuery->sum('total_price') / 100;

            $series[] = [
                'month_label' => $date->format('M'),
                'month_key' => $date->format('Y-m'),
                'bookings' => $bookings,
                'revenue' => round($revenue, 2),
            ];
        }

        return $series;
    }

    private function inventorySnapshot(): array
    {
        $statusCounts = Apartment::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $total = array_sum($statusCounts);
        $taken = $statusCounts['taken'] ?? 0;
        $free = $statusCounts['free'] ?? 0;
        $cleaning = $statusCounts['cleaning'] ?? 0;

        return [
            'free' => $free,
            'taken' => $taken,
            'cleaning' => $cleaning,
            'total' => $total,
            'occupancy_rate' => $total > 0 ? round(($taken / $total) * 100, 1) : 0,
            'available_rate' => $total > 0 ? round(($free / $total) * 100, 1) : 0,
        ];
    }

    public function updateBookingStatus($bookingId, $status)
    {
        $booking = Booking::find($bookingId);
        if ($booking) {
            $booking->update(['status' => $status]);
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        // Daily Bookings (Last 7 Days)
        $dailyBookings = [];
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('D'); // Mon, Tue...
            $dailyBookings[] = Booking::whereDate('created_at', $date)->count();
        }

        // Monthly Revenue (Last 12 Months)
        $revenueSeries = $this->revenueSeries(12);

        return view('livewire.admin.dashboard-stats', [
            'totalBookings' => Booking::count(),
            'totalRevenue' => Booking::sum('total_price'),
            'activeListings' => Apartment::where('status', '!=', 'taken')->count(),
            'totalUsers' => User::where('role', 'guest')->count(),
            'revenueThisMonth' => Booking::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('total_price'),
            'averageBookingValue' => Booking::avg('total_price'),
            'inventory' => $this->inventorySnapshot(),
            'recentBookings' => Booking::with(['user', 'apartment'])->latest()->take(5)->get(),
            'topApartments' => Apartment::withSum('bookings', 'total_price')
                ->orderByDesc('bookings_sum_total_price')
                ->take(3)
                ->get(),
            'chartData' => [
                'days' => $days,
                'dailyBookings' => $dailyBookings,
                'months' => array_column($revenueSeries, 'month_label'),
                'monthlyRevenue' => array_column($revenueSeries, 'revenue'),
                'monthlyBookings' => array_column($revenueSeries, 'bookings'),
            ],
        ]);
    }
}
