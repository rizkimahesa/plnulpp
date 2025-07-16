@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
            📊 Data dari Google Sheets
        </h1>

        @if(session('error'))
            <div class="mb-4 text-red-600 dark:text-red-400 font-semibold">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto w-full border border-gray-300 dark:border-gray-700 rounded-xl shadow scrollbar-thin">
            <table class="table table-bordered table-hover mb-0 min-w-[1200px] table-auto text-sm text-left divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gradient-to-r from-blue-600 to-indigo-500 text-white uppercase text-xs sticky top-0 z-10">
        <tr>
            @foreach ($data[0] as $header)
                <th class="px-2 py-1 border border-gray-300 dark:border-gray-600 font-semibold text-xs whitespace-nowrap">
                    {{ $header }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @foreach (array_slice($data, 1) as $index => $row)
            <tr class="{{ $index % 2 === 0 ? 'bg-gray-50 dark:bg-gray-900' : '' }} hover:bg-indigo-50 dark:hover:bg-indigo-700 transition duration-150">
                @foreach ($row as $cell)
                    <td class="px-2 py-1 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-xs break-words max-w-[200px]">
                        {{ $cell }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

    </div>

        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 italic">
            Geser ke kanan jika kolom terlalu banyak &raquo;
        </p>
    </div>
</div>
@endsection