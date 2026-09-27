<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $totalRevenue = Booking::whereIn('status', ['confirmed', 'checked_in', 'checked_out'])->sum('total_amount');

        $totalRooms = Room::where('is_active', true)->count();
        $occupiedNow = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedNow / $totalRooms) * 100, 1) : 0;

        $monthlyBookings = Booking::query()
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"), DB::raw('COUNT(*) as total'), DB::raw('SUM(total_amount) as revenue'))
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('ym')->orderBy('ym')->get();

        return view('admin.dashboard', [
            'totalRevenue' => $totalRevenue,
            'occupancyRate' => $occupancyRate,
            'totalBookings' => Booking::count(),
            'totalUsers' => User::count(),
            'totalRooms' => $totalRooms,
            'monthlyBookings' => $monthlyBookings,
            'recentBookings' => Booking::with(['room', 'guest'])->latest()->limit(8)->get(),
            'roomTypeBreakdown' => Room::select('type', DB::raw('count(*) as total'))->groupBy('type')->get(),
        ]);
    }

    /** Streams bookings + revenue as a downloadable CSV (opens natively in Excel). */
    public function exportCsv(Request $request): StreamedResponse
    {
        $from = $request->input('from', now()->subMonths(1)->toDateString());
        $to = $request->input('to', now()->toDateString());

        $bookings = Booking::with(['room', 'guest'])
            ->whereBetween('created_at', [$from, $to.' 23:59:59'])
            ->orderBy('created_at')
            ->get();

        $filename = 'luxstay-bookings-'.$from.'-to-'.$to.'.csv';

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference', 'Guest', 'Room', 'Type', 'Check-in', 'Check-out', 'Nights', 'Subtotal', 'Tax', 'Total', 'Status', 'Booked At']);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->booking_reference,
                    $b->guest->name,
                    $b->room->room_number,
                    $b->room->type,
                    $b->check_in->toDateString(),
                    $b->check_out->toDateString(),
                    $b->nights,
                    $b->subtotal,
                    $b->tax_amount,
                    $b->total_amount,
                    $b->status,
                    $b->created_at->toDateTimeString(),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
