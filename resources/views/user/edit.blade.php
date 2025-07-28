@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4">✏️ Edit Data IDPEL: {{ $id }}</h2>

    @if ($linkDriveHariIni)
    <div class="mb-6">
        <p class="text-gray-700 font-semibold mb-1">📁 Link Google Drive Hari Ini:</p>
        <a href="{{ $linkDriveHariIni }}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
            {{ $linkDriveHariIni }}
        </a>
    </div>
    @else
        <div class="mb-6 text-red-600 font-semibold">
            📁 Folder Google Drive untuk tanggal hari ini belum tersedia.
        </div>
    @endif

    <form method="POST" action="{{ route('data.p2tl.update', ['id' => $id]) }}">
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

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            📂 Simpan Perubahan
        </button>
    </form>
</div>
@endsection
