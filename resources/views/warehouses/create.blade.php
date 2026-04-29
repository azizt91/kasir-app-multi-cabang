@extends('layouts.app')
@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="pb-6 border-b-2 border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">➕ Tambah Gudang</h1>
            <p class="text-gray-600 mt-1">Buat gudang baru untuk cabang Anda.</p>
        </div>
        @if(session('error'))
            <div class="mt-6 bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
        @endif
        <form action="{{ route('warehouses.store') }}" method="POST" class="mt-8 bg-white rounded-xl shadow-md border border-gray-200 p-6 space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Gudang <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700">Cabang <span class="text-red-500">*</span></label>
                <select name="branch_id" id="branch_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Lokasi</label>
                <input type="text" name="location" id="location" value="{{ old('location') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Aktif</label>
            </div>
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('warehouses.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
