@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">
        📊 Data Pemakaian KWH
    </h1>

    {{-- Dropdown Filter Bulan --}}
    <form method="GET" action="{{ route('pemakaian.kwh') }}" class="mb-4">
        <label for="bulan" class="mr-2 text-sm text-gray-700 dark:text-gray-300">Pilih Bulan:</label>
        <select name="bulan" id="bulan" onchange="this.form.submit()"
        class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-600 p-2 rounded text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        @foreach ($sheetNames as $name)
            <option value="{{ $name }}" {{ $bulan == $name ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
    </form>

    {{-- Tabel Data --}}
    <div class="overflow-x-auto w-full border border-gray-300 dark:border-gray-700 rounded-xl shadow scrollbar-thin">
        <table class="min-w-full table-auto text-sm text-left divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gradient-to-r from-blue-600 to-indigo-500 text-white uppercase text-xs sticky top-0 z-10">
                <tr>
                    @foreach ($header as $col)
                        <th class="px-2 py-1 border border-gray-300 dark:border-gray-600 font-semibold text-xs whitespace-nowrap">
                            {{ $col }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($data as $index => $row)
                    <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }} hover:bg-indigo-50 dark:hover:bg-indigo-700 transition duration-150">
                        @foreach ($header as $i => $col)
                            <td class="px-2 py-1 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs break-words max-w-[200px]">
                                {{ $row[$i] ?? '-' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($header) }}" class="text-center py-4 text-sm text-gray-500 dark:text-gray-400 italic">
                            Tidak ada data tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 italic">
        Geser ke kanan jika kolom terlalu banyak &raquo;
    </p>
</div>
@endsection
