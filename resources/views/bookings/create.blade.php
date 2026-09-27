@extends('layouts.app')
@section('title', 'Book '.$room->name.' — LuxStay')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="bookingFlow('{{ route('bookings.availability', $room) }}', {{ $room->capacity }})">

    <h1 class="text-2xl font-serif">Book: {{ $room->name }}</h1>
    <p class="text-slate-500 text-sm">Room {{ $room->room_number }} &middot; ${{ number_format($room->price_per_night, 2) }} / night</p>

    <!-- Step indicator -->
    <div class="flex items-center gap-3 mt-6 mb-8 text-sm">
        <template x-for="(label, i) in ['Dates', 'Details', 'Review & Pay']" :key="i">
            <div class="flex items-center gap-2">
                <span :class="step >= i+1 ? 'bg-gold-500 text-ink-900' : 'bg-slate-200 text-slate-500'"
                      class="w-7 h-7 rounded-full flex items-center justify-center font-semibold" x-text="i+1"></span>
                <span :class="step >= i+1 ? 'text-ink-900 font-medium' : 'text-slate-400'" x-text="label"></span>
                <span x-show="i < 2" class="w-8 h-px bg-slate-300 mx-1"></span>
            </div>
        </template>
    </div>

    <form method="POST" action="{{ route('bookings.store', $room) }}" class="bg-white rounded-2xl shadow-lg p-8">
        @csrf

        <!-- STEP 1: Dates -->
        <div x-show="step === 1">
            <h2 class="font-semibold text-lg mb-4">Choose your dates</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Check-in</label>
                    <input type="date" name="check_in" x-model="checkIn" @change="checkAvailability()"
                           min="{{ now()->toDateString() }}" required class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Check-out</label>
                    <input type="date" name="check_out" x-model="checkOut" @change="checkAvailability()"
                           min="{{ now()->addDay()->toDateString() }}" required class="w-full rounded-lg border-slate-300">
                </div>
            </div>

            <div class="mt-4" x-show="checking">
                <span class="text-sm text-slate-400"><i class="fa-solid fa-circle-notch fa-spin"></i> Checking availability…</span>
            </div>

            <div class="mt-4" x-show="checked && available === false" x-cloak>
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> This room is not available for the selected dates. Please try a different range.
                </div>
            </div>

            <div class="mt-4" x-show="checked && available === true" x-cloak>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg px-4 py-3 text-sm">
                    <i class="fa-solid fa-circle-check mr-1"></i> Available! <span x-text="nights"></span> night(s) selected.
                </div>
            </div>

            <button type="button" @click="step = 2" :disabled="!(checked && available)"
                    class="mt-6 bg-ink-900 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-ink-800 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                Continue <i class="fa-solid fa-arrow-right ml-1"></i>
            </button>
        </div>

        <!-- STEP 2: Guest details -->
        <div x-show="step === 2" x-cloak>
            <h2 class="font-semibold text-lg mb-4">Guest details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Number of guests</label>
                    <input type="number" name="guests" x-model.number="guests" min="1" :max="capacity" required
                           class="w-full rounded-lg border-slate-300">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium mb-1">Special requests (optional)</label>
                <textarea name="special_requests" rows="3" maxlength="1000" placeholder="e.g. late check-in, high floor, allergies…"
                          class="w-full rounded-lg border-slate-300"></textarea>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" @click="step = 1" class="text-slate-500 hover:text-ink-900 font-medium px-4 py-2.5">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back
                </button>
                <button type="button" @click="step = 3" class="bg-ink-900 hover:bg-ink-800 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                    Continue to Review <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>
        </div>

        <!-- STEP 3: Review, pricing & payment -->
        <div x-show="step === 3" x-cloak>
            <h2 class="font-semibold text-lg mb-4">Review & payment</h2>

            <div class="bg-slate-50 rounded-xl p-5 text-sm space-y-2">
                <div class="flex justify-between"><span class="text-slate-500">Dates</span><span x-text="checkIn + ' → ' + checkOut"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Nights</span><span x-text="nights"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Guests</span><span x-text="guests"></span></div>
                <hr class="border-slate-200 my-2">
                <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span x-text="pricing ? '$' + pricing.subtotal.toFixed(2) : '—'"></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Taxes & fees</span><span x-text="pricing ? '$' + pricing.tax.toFixed(2) : '—'"></span></div>
                <div class="flex justify-between font-bold text-base pt-1"><span>Total</span><span x-text="pricing ? '$' + pricing.total.toFixed(2) : '—'"></span></div>
            </div>

            <h3 class="font-semibold mt-6 mb-2">Payment method</h3>
            <div class="space-y-2">
                <label class="flex items-center gap-3 border border-slate-200 rounded-lg px-4 py-3 cursor-pointer has-[:checked]:border-gold-500 has-[:checked]:bg-gold-50">
                    <input type="radio" name="payment_method" value="mock_card" x-model="paymentMethod" required class="text-gold-600">
                    <span><i class="fa-regular fa-credit-card mr-1"></i> Pay now (sandbox card — no real charge)</span>
                </label>
                <label class="flex items-center gap-3 border border-slate-200 rounded-lg px-4 py-3 cursor-pointer has-[:checked]:border-gold-500 has-[:checked]:bg-gold-50">
                    <input type="radio" name="payment_method" value="cash_on_arrival" x-model="paymentMethod" class="text-gold-600">
                    <span><i class="fa-solid fa-money-bill-wave mr-1"></i> Pay at hotel (Cash on Arrival)</span>
                </label>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" @click="step = 2" class="text-slate-500 hover:text-ink-900 font-medium px-4 py-2.5">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back
                </button>
                <button type="submit" :disabled="!paymentMethod"
                        class="bg-gold-500 disabled:opacity-40 hover:bg-gold-600 text-ink-900 font-semibold px-6 py-2.5 rounded-lg transition">
                    Confirm Reservation <i class="fa-solid fa-check ml-1"></i>
                </button>
            </div>
        </div>
    </form>
</div>

@push('head')
<script>
    function bookingFlow(availabilityUrl, capacity) {
        return {
            step: 1,
            checkIn: '', checkOut: '',
            guests: 1, capacity: capacity,
            paymentMethod: '',
            checking: false, checked: false, available: null,
            nights: 0, pricing: null,

            async checkAvailability() {
                if (!this.checkIn || !this.checkOut) return;
                this.checking = true; this.checked = false;
                try {
                    const res = await fetch(`${availabilityUrl}?check_in=${this.checkIn}&check_out=${this.checkOut}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    this.available = data.available;
                    this.nights = data.nights;
                    this.pricing = data.pricing;
                } catch (e) {
                    this.available = null;
                } finally {
                    this.checking = false; this.checked = true;
                }
            }
        }
    }
</script>
@endpush
@endsection
