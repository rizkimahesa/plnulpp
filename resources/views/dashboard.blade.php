@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto mt-6">
    <h1 class="text-2xl font-bold mb-4">🔍 Dashboard Pencarian Data</h1>

    <form method="GET" action="{{ route('dashboard') }}" class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="kategori" class="block font-medium text-sm text-gray-700 dark:text-white">Pilih Kategori</label>
                <select name="kategori" id="kategori" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
                    <option value="">Semua</option>
                    <option value="p2tl" {{ request('kategori') == 'p2tl' ? 'selected' : '' }}>P2TL</option>
                    <option value="harmet" {{ request('kategori') == 'harmet' ? 'selected' : '' }}>Harmet</option>
                    <option value="billing" {{ request('kategori') == 'billing' ? 'selected' : '' }}>Billing</option>
                    <option value="pem kwh" {{ request('kategori') == 'pem kwh' ? 'selected' : '' }}>Pemakaian KWH</option>
                </select>
            </div>

            <div>
                <label for="kolom" class="block font-medium text-sm text-gray-700 dark:text-white">Pilih Kolom</label>
                <select name="kolom" id="kolom" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
                    @if(!request('kategori') || request('kategori') == 'p2tl')
                        <optgroup label="Kolom P2TL">
                            <option value="IDPEL" {{ request('kolom') == 'IDPEL' ? 'selected' : '' }}>IDPEL</option>
                            <option value="NAMA" {{ request('kolom') == 'NAMA' ? 'selected' : '' }}>NAMA</option>
                        </optgroup>
                    @endif

                    @if(!request('kategori') || request('kategori') == 'harmet')
                        <optgroup label="Kolom Harmet">
                            <option value="IDPEL" {{ request('kolom') == 'IDPEL' ? 'selected' : '' }}>IDPEL</option>
                            <option value="NAMA" {{ request('kolom') == 'NAMA' ? 'selected' : '' }}>NAMA</option>
                        </optgroup>
                    @endif

                    @if(!request('kategori') || request('kategori') == 'billing')
                        <optgroup label="Kolom Billing">
                            <option value="IDPEL" {{ request('kolom') == 'IDPEL' ? 'selected' : '' }}>IDPEL</option>
                            <option value="NAMA" {{ request('kolom') == 'NAMA' ? 'selected' : '' }}>NAMA</option>
                        </optgroup>
                    @endif

                    @if(!request('kategori') || request('kategori') == 'pem kwh')
                        <optgroup label="Kolom Pem KWH">
                            <option value="IDPEL" {{ request('kolom') == 'IDPEL' ? 'selected' : '' }}>IDPEL</option>
                            <option value="NAMA" {{ request('kolom') == 'NAMA' ? 'selected' : '' }}>NAMA</option>
                        </optgroup>
                    @endif
                </select>
            </div>

            <div>
                <label for="search" class="block font-medium text-sm text-gray-700 dark:text-white">Kata Kunci</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cari</button>
        </div>
    </form>

    {{-- Tabel Data --}}
    @if((request('search') || request('kategori') || request('kolom')) && isset($data) && count($data) > 0)
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">Hasil Pencarian</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            @foreach(array_keys($data[0]) as $header)
                                <th class="px-4 py-2 text-left text-gray-600 dark:text-white">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                            <tr class="border-b border-gray-200 dark:border-gray-600">
                                @foreach($row as $cell)
                                    <td class="px-4 py-2 text-gray-800 dark:text-white">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif(request('search'))
        <div class="text-red-600 font-semibold">Data tidak ditemukan.</div>
    @endif

    {{-- Grafik Line Chart --}}
    @if(request('kategori') === 'pem kwh' && request('kolom') === 'IDPEL' && !empty($chartData))
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mt-6">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">Grafik Pemakaian KWH per Bulan</h2>
            <canvas id="pemkwhChart" height="100"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('pemkwhChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_column($chartData, 'label')) !!},
                    datasets: [{
                        label: 'PEMKWH',
                        data: {!! json_encode(array_column($chartData, 'value')) !!},
                        fill: false,
                        borderColor: 'rgba(59, 130, 246, 1)',
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        tension: 0.3,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'kWh'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#fff'
                            }
                        }
                    }
                }
            });
        </script>
    @endif
</div>
@endsection
