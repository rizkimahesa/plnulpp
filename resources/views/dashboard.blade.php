@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto mt-6">
    <h1 class="text-2xl font-bold mb-4">🔍 Dashboard Pencarian Data</h1>

    {{-- Form Pencarian --}}
    <form method="GET" action="{{ route('dashboard') }}" class="mb-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Pilih Kategori --}}
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

            {{-- Pilih Kolom --}}
            <div>
                <label for="kolom" class="block font-medium text-sm text-gray-700 dark:text-white">Pilih Kolom</label>
                <select name="kolom" id="kolom" class="mt-1 block w-full border-gray-300 dark:bg-gray-700 dark:text-white rounded-md shadow-sm">
                    @if(!request('kategori') || request('kategori') == 'p2tl')
                        <optgroup label="Kolom P2TL">
                            <option value="IDPEL" {{ request('kolom') == 'IDPEL' ? 'selected' : '' }}>IDPEL</option>
                            <option value="NO BA" {{ request('kolom') == 'NO BA' ? 'selected' : '' }}>NO BA</option>
                            <option value="NOREGISTER" {{ request('kolom') == 'NOREGISTER' ? 'selected' : '' }}>NOREGISTER</option>
                            <option value="Nama ID pelanggan" {{ request('kolom') == 'Nama ID pelanggan' ? 'selected' : '' }}>Nama ID pelanggan</option>
                        </optgroup>
                    @endif

                    @if(!request('kategori') || request('kategori') == 'harmet')
                        <optgroup label="Kolom Harmet">
                            <option value="ID PELANGGAN" {{ request('kolom') == 'ID PELANGGAN' ? 'selected' : '' }}>ID PELANGGAN</option>
                            <option value="Nama Pelanggan" {{ request('kolom') == 'Nama Pelanggan' ? 'selected' : '' }}>Nama Pelanggan</option>
                            <option value="NO. BA" {{ request('kolom') == 'NO. BA' ? 'selected' : '' }}>NO. BA</option>
                        </optgroup>
                    @endif

                    @if(!request('kategori') || request('kategori') == 'billing')
                        <optgroup label="Kolom Billing">
                            <option value="IDPEL" {{ request('kolom') == 'IDPEL' ? 'selected' : '' }}>IDPEL</option>
                            <option value="NAMA" {{ request('kolom') == 'NAMA' ? 'selected' : '' }}>NAMA</option>
                            <option value="BLTH" {{ request('kolom') == 'BLTH' ? 'selected' : '' }}>BLTH</option>
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

            {{-- Input Pencarian --}}
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
    @if((request('search') || request('kategori') || request('kolom')) && isset($data) && count($data) > 1)
        @php
            // Ambil baris pertama sebagai header
            $headers = $data[0];
            // Ambil data mulai baris kedua
            $rows = array_slice($data, 1);

            // Mapping header jadi uppercase untuk pencarian kolom
            $headerMap = array_map('strtoupper', $headers);
            $idpelIndex = array_search('IDPEL', $headerMap);
            $statusIndex = array_search('STATUS', $headerMap);

            // Fungsi format tanggal (jika cocok)
            function formatTanggal($value) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                    return \Carbon\Carbon::parse($value)->format('d-m-Y');
                }
                return $value;
            }
        @endphp

        <div class="overflow-x-auto border dark:border-gray-700 rounded-lg shadow">
            <table class="w-max whitespace-nowrap bg-white dark:bg-gray-800 text-sm">
                <thead class="bg-blue-600 text-white uppercase text-xs">
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-4 py-3 border border-gray-500">{{ $header }}</th>
                        @endforeach
                        <th class="px-4 py-3 border border-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($rows as $row)
                        @php
                            $idpelanggan = $idpelIndex !== false ? ($row[$idpelIndex] ?? null) : null;
                            $statusValue = $statusIndex !== false ? strtolower(trim($row[$statusIndex] ?? '')) : '';
                        @endphp
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            @foreach($row as $cell)
                                <td class="px-4 py-3 border border-gray-300 dark:border-gray-700 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                    {{ formatTanggal($cell) }}
                                </td>
                            @endforeach
                            <td class="px-4 py-3 border border-gray-300 dark:border-gray-700 whitespace-nowrap text-gray-900 dark:text-gray-100 space-x-1">
                                @if ($statusValue === 'lunas' && $idpelanggan)
                                    <a href="{{ route('data.realisasi.byIdpel', ['idpel' => $idpelanggan]) }}"
                                       class="inline-block px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-semibold">
                                        🔗 Lunas
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif(request('search'))
        <div class="text-red-600 font-semibold">Data tidak ditemukan.</div>
    @endif  

    {{-- Grafik Line Chart --}}
    @if(request('kategori') === 'pem kwh' && in_array(request('kolom'), ['IDPEL', 'NAMA']) && !empty($chartData))
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
                            title: { display: true, text: 'kWh' }
                        },
                        x: {
                            title: { display: true, text: 'Bulan' }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            labels: { color: '#fff' }
                        }
                    }
                }
            });
        </script>
    @endif
</div>
@endsection
