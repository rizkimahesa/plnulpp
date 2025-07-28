@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
            📊 Data dari Google Sheets
        </h1>

        {{-- Tampilkan error jika ada --}}
        @if(session('error') || ($error ?? false))
            <div class="mb-4 text-red-600 dark:text-red-400 font-semibold">
                {{ session('error') ?? $error }}
            </div>
        @endif

        @if (!empty($data) && is_array($data) && isset($data[0]))
            @php
                $headers = $data[0];
                $body = array_slice($data, 1);
                $idpelIndex = array_search('IDPEL', array_map('strtoupper', $headers));
                $statusIndex = array_search('STATUS', array_map('strtoupper', $headers));
            @endphp

            <div class="overflow-x-auto w-full border border-gray-300 dark:border-gray-700 rounded-xl shadow scrollbar-thin">
                <table class="table table-bordered table-hover mb-0 min-w-[1200px] table-auto text-sm text-left divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-blue-600 to-indigo-500 text-white uppercase text-xs sticky top-0 z-10">
                        <tr>
                            @foreach ($headers as $header)
                                <th class="px-2 py-1 border border-gray-300 dark:border-gray-600 font-semibold text-xs whitespace-nowrap">
                                    {{ $header }}
                                </th>
                            @endforeach
                            <th class="px-2 py-1 border border-gray-300 dark:border-gray-600 font-semibold text-xs whitespace-nowrap">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($body as $index => $row)
                            @php
                                $idpelanggan = ($idpelIndex !== false && isset($row[$idpelIndex])) 
                                ? preg_replace('/\s+/', '', strtoupper(trim($row[$idpelIndex]))) 
                                : null;
                                $statusValue = ($statusIndex !== false && isset($row[$statusIndex])) ? strtolower(trim($row[$statusIndex])) : '';
                            @endphp

                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }} hover:bg-indigo-50 dark:hover:bg-indigo-700 transition duration-150">

                                @foreach ($headers as $i => $header)
                                    <td class="px-2 py-1 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs break-words max-w-[200px]">
                                        {{ $row[$i] ?? '' }}
                                    </td>
                                @endforeach

                                <td class="px-2 py-1 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs whitespace-nowrap space-x-1">
                                    @if (request()->routeIs('data.p2tl') && $statusValue === 'lunas' && $idpelanggan)
                                        <a href="{{ route('data.realisasi.byIdpel', ['idpel' => $idpelanggan]) }}"
                                           class="inline-block px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-semibold">
                                            🔗 Lunas
                                        </a>
                                    @endif

                                    @if ($idpelanggan && request()->routeIs('data.p2tl'))
                                        <a href="{{ route('data.p2tl.edit', ['id' => $idpelanggan]) }}"
                                           class="inline-block px-2 py-1 bg-yellow-400 hover:bg-yellow-500 text-white rounded text-xs font-semibold">
                                            ✏️ Edit
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($headers) + 1 }}" class="text-center py-4 text-sm text-gray-500 dark:text-gray-400 italic">
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
        @else
            <div class="text-center text-gray-600 dark:text-gray-300 text-sm mt-8">
                <p>📭 Tidak ada data yang ditampilkan dari Google Sheets.</p>
            </div>
        @endif
    </div>
</div>
@endsection
