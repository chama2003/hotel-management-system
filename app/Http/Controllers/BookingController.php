<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Services\BookingPricingService;
use App\Services\BookingService;
use App\Services\RoomAvailabilityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingPricingService $pricingService,
        protected RoomAvailabilityService $availabilityService,
        protected BookingService $bookingService,
    ) {}

    /** Step 1: date + guest count for a specific room. */
    public function create(Room $room): View
    {
        abort_unless($room->is_active, 404);

        return view('bookings.create', ['room' => $room]);
    }

    /**
     * AJAX endpoint used by the date picker to grey out unavailable dates
     * and re-validate the moment the guest changes check-in/check-out.
     */
    public function checkAvailability(Request $request, Room $room)
    {
        $data = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $available = $this->availabilityService->isAvailable($room->id, $data['check_in'], $data['check_out']);
        $nights = Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out']));

        return response()->json([
            'available' => $available,
            'nights' => $nights,
            'pricing' => $available ? $this->pricingService->calculate($room, Carbon::parse($data['check_in']), Carbon::parse($data['check_out'])) : null,
        ]);
    }

    /** Step 2: review pricing summary + special requests, then confirm. */
    public function store(Request $request, Room $room): RedirectResponse
    {
        $data = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:'.max(1, $room->capacity)],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:mock_card,cash_on_arrival'],
        ]);

        try {
            $booking = $this->bookingService->createForGuest($request->user(), $room, $data);
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['check_in' => $exception->getMessage()])->withInput();
        }

        return redirect()
            ->route('bookings.confirmation', $booking)
            ->with('status', 'Your reservation is confirmed! Reference: '.$booking->booking_reference);
    }

    public function confirmation(Booking $booking): View
    {
        $this->authorizeGuestOwnsBooking($booking);
        $booking->load(['room.images', 'payment', 'invoice']);

        return view('bookings.confirmation', ['booking' => $booking]);
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeGuestOwnsBooking($booking);
        abort_unless($booking->isCancellable(), 403, 'This booking can no longer be cancelled online.');

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('reason', 'Cancelled by guest'),
        ]);

        if ($booking->payment && $booking->payment->status === 'paid') {
            $booking->payment->update(['status' => 'refunded']);
        }

        return back()->with('status', 'Booking cancelled. A confirmation has been sent to your email.');
    }

    /** Streams a PDF invoice for the guest to download from their dashboard. */
    public function invoice(Booking $booking)
    {
        $this->authorizeGuestOwnsBooking($booking);
        $booking->load(['room', 'guest', 'payment', 'invoice']);

        $pdf = Pdf::loadView('bookings.invoice-pdf', ['booking' => $booking]);

        return $pdf->download($booking->invoice->invoice_number.'.pdf');
    }

    private function authorizeGuestOwnsBooking(Booking $booking): void
    {
        $user = request()->user();
        abort_unless($user && ($user->isAdmin() || $user->isStaff() || $booking->user_id === $user->id), 403);
    }

    private function priceBreakdown(Room $room, int $nights): array
    {
        return $this->pricingService->calculate($room, now()->startOfDay()->addDays(1), now()->startOfDay()->addDays(1 + $nights));
    }
}
