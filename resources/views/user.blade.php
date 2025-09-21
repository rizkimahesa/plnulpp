@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false, userId: null, userName: '' }" class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <h1 class="text-xl font-bold mb-4">Daftar Akun Terdaftar</h1>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Password</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @foreach ($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-gray-400 italic">••••••••••</td>
                    <td class="px-6 py-4">
                        <button 
                            @click="showModal = true; userId = {{ $user->id }}; userName = '{{ $user->name }}'"
                            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-1 rounded transition duration-200">
                            Reset Password
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div 
        x-show="showModal" 
        x-transition.opacity 
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        x-cloak>
        <div 
            x-transition.scale 
            class="bg-white rounded-2xl shadow-lg p-6 max-w-sm w-full">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Konfirmasi Reset Password</h2>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin mereset password untuk <span class="font-semibold text-red-500" x-text="userName"></span>?</p>

            <form :action="'/user/' + userId + '/reset-password'" method="POST">
                @csrf
                <div class="flex justify-end space-x-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded bg-red-500 hover:bg-red-600 text-white transition">
                        Ya, Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Tambahkan Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
