@extends('layouts.app')
@section('title', 'User Management — LuxStay')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-serif">User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-gold-500 hover:bg-gold-600 text-ink-900 font-semibold text-sm px-4 py-2 rounded-full transition">
            <i class="fa-solid fa-user-plus mr-1"></i> Add Staff / Admin
        </a>
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email" class="rounded-lg border-slate-300 text-sm flex-1">
        <select name="role" class="rounded-lg border-slate-300 text-sm">
            <option value="">All roles</option>
            @foreach(['admin','staff','customer'] as $r)
                <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        <button class="bg-ink-900 text-white px-4 rounded-lg text-sm">Filter</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase text-xs">
                <tr><th class="text-left py-3 px-4">Name</th><th class="text-left">Email</th><th class="text-left">Role</th><th class="text-left">Status</th><th class="text-right px-4">Actions</th></tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-t border-slate-100">
                        <td class="py-3 px-4">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="capitalize">{{ $user->role }}</td>
                        <td>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 text-right">
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggleActive', $user) }}" class="inline">
                                    @csrf
                                    <button class="text-xs text-slate-500 hover:text-ink-900 underline">
                                        {{ $user->is_active ? 'Deactivate' : 'Reactivate' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
</div>
@endsection
