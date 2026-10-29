<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = HotelSetting::query()->orderBy('key')->get()->keyBy('key');

        return view('admin.settings', [
            'settings' => $settings,
            'taxRate' => (float) ($settings['tax_rate']->value ?? config('booking.tax_rate', 0.10)),
            'freeCancellationHours' => (int) ($settings['free_cancellation_hours']->value ?? config('booking.free_cancellation_hours', 48)),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:1'],
            'free_cancellation_hours' => ['required', 'integer', 'min:0', 'max:720'],
        ]);

        HotelSetting::setValue('tax_rate', (string) $validated['tax_rate'], 'Percentage tax applied to each booking subtotal.');
        HotelSetting::setValue('free_cancellation_hours', (string) $validated['free_cancellation_hours'], 'Allowed free-cancellation window in hours before check-in.');

        config()->set('booking.tax_rate', (float) $validated['tax_rate']);
        config()->set('booking.free_cancellation_hours', (int) $validated['free_cancellation_hours']);

        return back()->with('status', 'Hotel settings updated successfully.');
    }
}
