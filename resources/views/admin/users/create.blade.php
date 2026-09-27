@extends('layouts.app')
@section('title', 'Add User — LuxStay')

@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-serif mb-6">Add Staff / Admin Account</h1>
    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-2xl shadow p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Full name</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <select name="role" required class="w-full rounded-lg border-slate-300">
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
                <option value="customer">Customer</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Temporary password</label>
            <input type="password" name="password" required class="w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Confirm password</label>
            <input type="password" name="password_confirmation" required class="w-full rounded-lg border-slate-300">
        </div>
        <button class="w-full bg-ink-900 hover:bg-ink-800 text-white font-semibold py-2.5 rounded-lg transition">Create Account</button>
    </form>
</div>
@endsection
