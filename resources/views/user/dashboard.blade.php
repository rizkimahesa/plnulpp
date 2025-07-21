`@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Form pencarian dan filter --}}
        <form method="GET" action="{{ route('user.dashboard') }}" class="mb-6 flex flex-wrap gap-3 items-center">
            <input 
                type="text" 
                name="search" 
                value="{{ request('search') }}" 
                placeholder="Cari data..." 
                class="px-3 py-2 border border-gray-300 rounded-md w-full sm:w-1/4 bg-white text-black placeholder-gray-500 dark:bg-gray-800 dark:text-white"
            />

            <select 
                name="kategori" 
                class="px-3 py-2 border border-gray-300 rounded-md w-full sm:w-1/6 bg-white text-black dark:bg-gray-800 dark:text-white"
            >
                <option value="">-- Semua Kategori --</option>
                <option value="billing" {{ request('kategori') == 'billing' ? 'selected' : '' }}>Billing</option>
                <option value="p2tl" {{ request('kategori') == 'p2tl' ? 'selected' : '' }}>P2TL</option>
                <option value="harmet" {{ request('kategori') == 'harmet' ? 'selected' : '' }}>Harmet</option>
            </select>

            <select 
                name="kolom" 
                class="px-3 py-2 border border-gray-300 rounded-md w-full sm:w-1/6 bg-white text-black dark:bg-gray-800 dark:text-white"
            >
                <option value="">-- Semua Kolom --</option>

                @if(!request('kategori') || request('kategori') == 'p2tl')
                    <optgroup label="Kolom P2TL">
                        <option value="Idpel" {{ request('kolom') == 'Idpel' ? 'selected' : '' }}>Idpel</option>
                        <option value="NO BA" {{ request('kolom') == 'NO BA' ? 'selected' : '' }}>NO BA</option>
                        <option value="NOREGISTER" {{ request('kolom') == 'NOREGISTER' ? 'selected' : '' }}>NOREGISTER</option>
                        <option value="Nama ID pelanggan" {{ request('kolom') == 'Nama ID pelanggan' ? 'selected' : '' }}>Nama ID pelanggan</option>
                    </optgroup>
                @endif

                @if(!request('kategori') || request('kategori') == 'billing')
                    <optgroup label="Kolom Billing">
                        <option value="IDPEL" {{ request('kolom') == 'IDPEL' ? 'selected' : '' }}>IDPEL</option>
                        <option value="NAMA" {{ request('kolom') == 'NAMA' ? 'selected' : '' }}>NAMA</option>
                        <option value="BLTH" {{ request('kolom') == 'BLTH' ? 'selected' : '' }}>BLTH</option>
                    </optgroup>
                @endif

                @if(!request('kategori') || request('kategori') == 'harmet')
                    <optgroup label="Kolom Harmet">
                        <option value="ID PELANGGAN" {{ request('kolom') == 'ID PELANGGAN' ? 'selected' : '' }}>ID PELANGGAN</option>
                        <option value="Nama Pelanggan" {{ request('kolom') == 'Nama Pelanggan' ? 'selected' : '' }}>Nama Pelanggan</option>
                        <option value="NO. BA" {{ request('kolom') == 'NO. BA' ? 'selected' : '' }}>NO. BA</option>
                    </optgroup>
                @endif
            </select>


            <button 
                type="submit" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold text-sm rounded-md shadow-md transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-300"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                </svg>
                Cari
            </button>
        </form>

        {{-- Tabel Data --}}
        @if((request('search') || request('kategori') || request('kolom')) && isset($data) && count($data) > 0)
            <div class="overflow-x-auto border dark:border-gray-700 rounded-lg shadow">
                <table class="w-max whitespace-nowrap bg-white dark:bg-gray-800 text-sm">
                    <thead class="bg-blue-600 text-white uppercase text-xs">
                        <tr>
                            @foreach ($data[0] as $header)
                                <th class="px-4 py-3 border border-gray-500">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach (array_slice($data, 1) as $row)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                @foreach ($row as $cell)
                                    <td class="px-4 py-3 border border-gray-300 dark:border-gray-700 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $cell }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-300 mt-4">Data tidak ditemukan.</p>
        @endif

    </div>
</div>
@endsection