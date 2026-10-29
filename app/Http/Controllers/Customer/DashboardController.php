<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $bookings = $user->bookings()->with('room.images')->latest('check_in')->get();

        return view('customer.dashboard', [
            'upcoming' => $bookings->whereIn('status', ['pending', 'confirmed'])->where('check_in', '>=', now()->toDateString()),
            'active' => $bookings->where('status', 'checked_in'),
            'past' => $bookings->whereIn('status', ['checked_out', 'cancelled', 'no_show'])
                ->merge($bookings->where('check_out', '<', now()->toDateString())->where('status', '!=', 'checked_in')),
        ]);
    }

    public function editProfile(Request $request): View
    {
        return view('customer.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('status', 'Profile updated.');
    }
}
