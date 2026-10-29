@extends('layouts.app')
@section('title', 'Add Room — LuxStay')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-serif mb-6">Add New Room</h1>
    <form method="POST" action="{{ route('admin.rooms.store') }}" class="bg-white rounded-2xl shadow p-6">
        @include('admin.rooms._form')
    </form>
</div>
@endsection
