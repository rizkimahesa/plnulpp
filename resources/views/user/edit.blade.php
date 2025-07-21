@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <h2 class="text-xl font-bold mb-4">✏️ Edit Data IDPEL: {{ $id }}</h2>

    <form method="POST" action="{{ route('data.p2tl.update', ['id' => $id]) }}">
        @csrf

        @php
            $editableHeaders = [
            'TANGGAL SP1', 'TANGGAL SP2', 'TANGGAL SP3',
            'TANGGAL Peringatan 1', 'Tanggal Peringatan 2',
            'ket pangilan 2', 'ket pangilan 3',
            'ket peringatan 1', 'ket peringatan 2',
            ];
        @endphp

        @foreach ($editableHeaders as $header)
            @php
                $colIndex = array_search($header, $headers);
                $value = $colIndex !== false && isset($row[$colIndex]) ? $row[$colIndex] : '';
            @endphp
            <div class="mb-4">
                <label class="block font-semibold text-sm text-gray-700">{{ $header }}</label>
                <input type="text" name="{{ $header }}" value="{{ $value }}" class="w-full p-2 border rounded">
            </div>
        @endforeach

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            💾 Simpan Perubahan
        </button>
    </form>
</div>
@endsection
