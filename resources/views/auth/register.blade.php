@extends('layouts.app')
@section('title', 'Create account — LuxStay')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-16 bg-gradient-to-b from-ink-900 to-slate-100">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center mb-6">
            <i class="fa-solid fa-hotel text-3xl text-gold-500"></i>
            <h1 class="text-2xl font-serif mt-2">Join LuxStay</h1>
            <p class="text-sm text-slate-500">Create a guest account to book your stay</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Phone (optional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <button class="w-full bg-gold-500 hover:bg-gold-600 text-ink-900 font-semibold py-2.5 rounded-lg transition">
                Create Account
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Already registered? <a href="{{ route('login') }}" class="text-gold-600 font-medium">Sign in</a>
        </p>
    </div>
</div>
@endsection
