@extends('layouts.app')
@section('title', 'Admin Dashboard — LuxStay')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-serif">Admin Dashboard</h1>
        <a href="{{ route('admin.export.bookings') }}" class="bg-ink-900 hover:bg-ink-800 text-white font-semibold text-sm px-4 py-2 rounded-full transition">
            <i class="fa-solid fa-file-csv mr-1"></i> Export Bookings CSV
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-slate-400 uppercase">Total Revenue</p>
            <p class="text-2xl font-bold mt-1">${{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-slate-400 uppercase">Occupancy Rate</p>
            <p class="text-2xl font-bold mt-1">{{ $occupancyRate }}%</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-slate-400 uppercase">Total Bookings</p>
            <p class="text-2xl font-bold mt-1">{{ $totalBookings }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-slate-400 uppercase">Registered Users</p>
            <p class="text-2xl font-bold mt-1">{{ $totalUsers }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-4">Monthly Bookings & Revenue</h2>
            <canvas id="bookingsChart" height="110"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="font-semibold mb-4">Rooms by Type</h2>
            <ul class="space-y-2 text-sm">
                @foreach($roomTypeBreakdown as $row)
                    <li class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="capitalize">{{ $row->type }}</span><span class="font-semibold">{{ $row->total }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="text-xs text-slate-400 mt-4">Total active rooms: {{ $totalRooms }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mt-8">
        <h2 class="font-semibold mb-4">Recent Bookings</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 uppercase text-xs border-b border-slate-100">
                    <th class="py-2">Reference</th><th>Guest</th><th>Room</th><th>Dates</th><th>Total</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $b)
                    <tr class="border-b border-slate-50">
                        <td class="py-2 font-mono">{{ $b->booking_reference }}</td>
                        <td>{{ $b->guest->name }}</td>
                        <td>{{ $b->room->room_number }}</td>
                        <td>{{ $b->check_in->format('M j') }}–{{ $b->check_out->format('M j') }}</td>
                        <td>${{ number_format($b->total_amount, 2) }}</td>
                        <td><span class="text-xs px-2 py-0.5 rounded-full {{ $b->statusBadgeColor() }}">{{ ucfirst($b->status) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('head')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const labels = @json($monthlyBookings->pluck('ym'));
        const counts = @json($monthlyBookings->pluck('total'));
        const revenue = @json($monthlyBookings->pluck('revenue'));

        new Chart(document.getElementById('bookingsChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'Bookings', data: counts, backgroundColor: '#c9a14a', yAxisID: 'y' },
                    { label: 'Revenue ($)', data: revenue, type: 'line', borderColor: '#16202b', backgroundColor: '#16202b', yAxisID: 'y1', tension: 0.3 },
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { position: 'left', beginAtZero: true },
                    y1: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false } },
                }
            }
        });
    });
</script>
@endpush
@endsection
