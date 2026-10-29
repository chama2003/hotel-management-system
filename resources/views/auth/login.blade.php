@extends('layouts.app')
@section('title', 'Sign in — LuxStay')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-16 bg-gradient-to-b from-ink-900 to-slate-100">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center mb-6">
            <i class="fa-solid fa-hotel text-3xl text-gold-500"></i>
            <h1 class="text-2xl font-serif mt-2">Welcome back</h1>
            <p class="text-sm text-slate-500">Sign in to manage your stay</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-gold-600">
                Remember me
            </label>
            <button class="w-full bg-ink-900 hover:bg-ink-800 text-white font-semibold py-2.5 rounded-lg transition">
                Sign In
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            No account? <a href="{{ route('register') }}" class="text-gold-600 font-medium">Create one</a>
        </p>

        <div class="mt-6 bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs text-slate-500">
            <strong>Demo logins</strong> (password: <code>password</code>)<br>
            admin@luxstay.test · staff@luxstay.test · guest@luxstay.test
        </div>
    </div>
</div>
@endsection
