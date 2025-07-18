@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-700">Ini adalah dashboard pengguna biasa.</p>

            <div class="mt-6">
                <ul class="list-disc ml-5 text-gray-600">
                    <li>🔍 Lihat data pribadi</li>
                    <li>📄 Akses informasi umum</li>
                    <li>🕒 Cek aktivitas terbaru</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
