@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false }" class="max-w-4xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4">✏️ Edit Data IDPEL: {{ $id }}</h2>

    @if ($linkDriveHariIni)
    <div class="mb-6">
        <p class="text-gray-700 font-semibold mb-1">📁 Link Google Drive:</p>
        <a href="{{ $linkDriveHariIni }}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
            {{ $linkDriveHariIni }}
        </a>
    </div>
    @else
        <div class="mb-6 text-red-600 font-semibold">
            📁 Folder Google Drive belum tersedia.
        </div>
    @endif

    <form id="editForm" method="POST" action="{{ route('data.p2tl.update', ['id' => $id]) }}">
        @csrf

        @php
            $editableHeaders = [
                17 => 'TANGGAL SP1',
                18 => 'TANGGAL SP2',
                19 => 'TANGGAL SP3',
                20 => 'TANGGAL Peringatan 1',
                21 => 'Tanggal Peringatan 2',
                22 => 'Keterangan Panggilan 2',
                23 => 'Keterangan Panggilan 3',
                24 => 'Keterangan Peringatan 1',
                25 => 'Keterangan Peringatan 2',
            ];

            $tanggalFields = ['TANGGAL SP1', 'TANGGAL SP2', 'TANGGAL SP3', 'TANGGAL Peringatan 1', 'Tanggal Peringatan 2'];
        @endphp

        @foreach ($editableHeaders as $colIndex => $label)
            @php
                $value = isset($row[$colIndex]) ? $row[$colIndex] : '';

                // Format value menjadi YYYY-MM-DD jika cocok dengan pola tanggal
                if (in_array($label, $tanggalFields) && !empty($value)) {
                    $date = \Carbon\Carbon::parse($value)->format('Y-m-d');
                } else {
                    $date = $value;
                }
            @endphp

            <div class="mb-4">
                <label class="block font-semibold text-sm text-gray-700">{{ $label }}</label>
                <input 
                    type="{{ in_array($label, $tanggalFields) ? 'date' : 'text' }}" 
                    name="col_{{ $colIndex }}" 
                    value="{{ $date }}" 
                    class="w-full p-2 border rounded text-black">
            </div>
        @endforeach

        <button 
            type="button" 
            @click="showModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
            📂 Simpan Perubahan
        </button>
    </form>

    <!-- Modal Konfirmasi -->
    <div 
        x-show="showModal" 
        x-transition.opacity 
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        x-cloak>
        <div x-transition.scale class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Konfirmasi Simpan</h2>
            <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menyimpan perubahan data IDPEL <span class="font-semibold text-blue-600">{{ $id }}</span>?</p>

            <div class="flex justify-end space-x-3">
                <button 
                    type="button" 
                    @click="showModal = false"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800 transition">
                    Batal
                </button>
                <button 
                    type="button" 
                    @click="document.getElementById('editForm').submit()"
                    class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white transition">
                    Ya, Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Alpine.js --}}
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
