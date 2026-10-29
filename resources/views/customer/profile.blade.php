@extends('layouts.app')
@section('title', 'My Profile — LuxStay')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-serif mb-6">My Profile</h1>

    <form method="POST" action="{{ route('customer.profile.update') }}" class="bg-white rounded-2xl shadow p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium mb-1">Full name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-400">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Address</label>
            <textarea name="address" rows="2" class="w-full rounded-lg border-slate-300">{{ old('address', $user->address) }}</textarea>
        </div>
        <hr class="border-slate-100">
        <p class="text-sm text-slate-400">Leave password blank to keep it unchanged.</p>
        <div>
            <label class="block text-sm font-medium mb-1">New password</label>
            <input type="password" name="password" class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Confirm new password</label>
            <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300">
        </div>
        <button class="bg-ink-900 hover:bg-ink-800 text-white font-semibold px-6 py-2.5 rounded-lg transition">Save Changes</button>
    </form>
</div>
@endsection
