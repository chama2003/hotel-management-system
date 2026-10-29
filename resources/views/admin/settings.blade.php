@extends('layouts.app')

@section('title', 'Hotel Settings')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between gap-3 mb-6">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-indigo-500 font-semibold">Admin</p>
                <h1 class="text-3xl font-serif text-slate-900">Hotel Settings</h1>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="tax_rate" class="block text-sm font-semibold text-slate-700 mb-2">Tax rate</label>
                        <input id="tax_rate" name="tax_rate" type="number" step="0.01" min="0" max="1" value="{{ old('tax_rate', $taxRate) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:outline-none">
                        <p class="text-xs text-slate-500 mt-2">Value expressed as a decimal, e.g. 0.10 = 10%.</p>
                    </div>

                    <div>
                        <label for="free_cancellation_hours" class="block text-sm font-semibold text-slate-700 mb-2">Free cancellation window</label>
                        <input id="free_cancellation_hours" name="free_cancellation_hours" type="number" min="0" max="720" value="{{ old('free_cancellation_hours', $freeCancellationHours) }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:outline-none">
                        <p class="text-xs text-slate-500 mt-2">Hours before check-in guests can cancel for free.</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-indigo-600 text-white px-5 py-3 font-semibold hover:bg-indigo-500 transition">Save settings</button>
                </div>
            </form>
        </div>
    </div>
@endsection
