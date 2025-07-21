<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;

class SpreedsheetController extends Controller
{
    private $spreadsheetId = '1qOjBDKZ6ZIvrP_otBfqXc9EA1PfBjzhPl0wBwc9Fk5M';
    private $sheetName = 'Sheet1';
    private $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';

    public function data(Request $request)
    {
        $range = '!A:AI';
        $p2tlUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$range}?key={$this->apiKey}";
        $p2tlResponse = Http::get($p2tlUrl);

        $p2tlRows = $p2tlResponse->json()['values'] ?? [];
        $p2tlHeader = $p2tlRows[0] ?? [];
        $p2tlBody = array_slice($p2tlRows, 1);
        $p2tlIndex = array_flip(array_map('strtolower', $p2tlHeader));

        $keyword = strtolower($request->input('search'));
        $filterKategori = strtolower($request->input('kategori'));
        $filterKolom = $request->input('kolom');

        // Filter data
        if ($keyword || $filterKategori || $filterKolom) {
            $p2tlBody = array_filter($p2tlBody, function ($row) use ($keyword, $filterKategori, $filterKolom, $p2tlIndex) {
                $matchSearch = true;
                if ($keyword) {
                    $matchSearch = false;
                    foreach ($row as $cell) {
                        if (stripos($cell, $keyword) !== false) {
                            $matchSearch = true;
                            break;
                        }
                    }
                }

                $matchKategori = true;
                if ($filterKategori && isset($p2tlIndex['kategori'])) {
                    $idx = $p2tlIndex['kategori'];
                    $matchKategori = isset($row[$idx]) && strtolower($row[$idx]) === $filterKategori;
                }

                $matchKolom = true;
                if ($filterKolom && isset($p2tlIndex[strtolower($filterKolom)])) {
                    $idx = $p2tlIndex[strtolower($filterKolom)];
                    $matchKolom = isset($row[$idx]) && stripos($row[$idx], $keyword) !== false;
                }

                return $matchSearch && $matchKategori && $matchKolom;
            });
        }

        $p2tlData = [$p2tlHeader, ...$p2tlBody];

        // Cari IDPEL lunas
        $idpelLunas = [];
        if (isset($p2tlIndex['status']) && isset($p2tlIndex['idpel'])) {
            foreach ($p2tlBody as $row) {
                if (isset($row[$p2tlIndex['status']]) && strtolower($row[$p2tlIndex['status']]) === 'lunas') {
                    $idpelLunas[] = $row[$p2tlIndex['idpel']];
                }
            }
        }

        // Ambil realisasi jika IDPEL lunas ada
        $realisasiData = [];
        if (!empty($idpelLunas)) {
            $realSpreadsheetId = '1_gtHDcSetTEggCVeLt1H_nx_25rXXOrvM0BMWa6plfE';
            $realRange = '!A:AI';
            $realUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$realSpreadsheetId}/values/{$realRange}?key={$this->apiKey}";
            $realResponse = Http::get($realUrl);

            if ($realResponse->successful()) {
                $realRows = $realResponse->json()['values'] ?? [];
                $realHeader = $realRows[0] ?? [];
                $realBody = array_slice($realRows, 1);
                $realIndex = array_flip(array_map('strtolower', $realHeader));

                if (isset($realIndex['idpel'])) {
                    $filteredReal = array_filter($realBody, function ($row) use ($realIndex, $idpelLunas) {
                        return isset($row[$realIndex['idpel']]) && in_array($row[$realIndex['idpel']], $idpelLunas);
                    });
                    $realisasiData = [$realHeader, ...$filteredReal];
                }
            }
        }

        return view('data', [
            'data' => $p2tlData,
            'realisasiData' => $realisasiData,
        ]);
    }

    public function edit($id)
    {
        $client = new Client();
        $client->setApplicationName('Laravel Google Sheets');
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig(storage_path('app/credentials.json'));

        $service = new Sheets($client);
        $range = $this->sheetName . '!A:AI';
        $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $headers = $values[0];
        $dataRows = array_slice($values, 1);
        $idpelIndex = array_search('Idpel', $headers);
        $rowToEdit = null;

        foreach ($dataRows as $row) {
            if (isset($row[$idpelIndex]) && $row[$idpelIndex] == $id) {
                $rowToEdit = $row;
                break;
            }
        }

        if (!$rowToEdit) {
            return redirect()->back()->with('error', 'Data dengan IDPEL tersebut tidak ditemukan.');
        }

        return view('user.edit', [
            'id' => $id,
            'headers' => $headers,
            'row' => $rowToEdit,
        ]);
    }

    public function update(Request $request, $id)
{
    // Inisialisasi Google Client & Sheets Service
    $client = new Client();
    $client->setApplicationName('Laravel Google Sheets');
    $client->setScopes([Sheets::SPREADSHEETS]);
    $client->setAuthConfig(storage_path('app/credentials.json'));

    $service = new Sheets($client);

    // Ambil data semua baris dari Sheet
    $range = $this->sheetName . '!A:AI';
    $response = $service->spreadsheets_values->get($this->spreadsheetId, $range);
    $data = $response->getValues();

    if (empty($data) || !isset($data[0])) {
        return redirect()->route('data.p2tl')->with('error', 'Data spreadsheet kosong atau header tidak ditemukan.');
    }

    // Header & indeks kolom
    $headers = $data[0];
    $headersUpper = array_map('strtoupper', $headers);
    $idpelIndex = array_search('IDPEL', $headersUpper);

    if ($idpelIndex === false) {
        return redirect()->route('data.p2tl')->with('error', 'Kolom IDPEL tidak ditemukan di header.');
    }

    // Temukan baris berdasarkan IDPEL
    $rowIndex = null;
    foreach ($data as $i => $row) {
        if (isset($row[$idpelIndex]) && $row[$idpelIndex] == $id) {
            $rowIndex = $i;
            break;
        }
    }

    if ($rowIndex === null) {
        return redirect()->route('data.p2tl')->with('error', 'ID tidak ditemukan dalam spreadsheet.');
    }

    $editableHeaders = [
        'TANGGAL SP1', 'TANGGAL SP2', 'TANGGAL SP3',
        'TANGGAL Peringatan 1', 'Tanggal Peringatan 2',
        'ket pangilan 2', 'ket pangilan 3',
        'ket peringatan 1', 'ket peringatan 2',
    ];


    // Siapkan baris yang ingin diperbarui
    $maxCols = max(count($headers), 35);
    $data[$rowIndex] = array_pad($data[$rowIndex], $maxCols, '');

    foreach ($editableHeaders as $header) {
        $colIndex = array_search(strtoupper($header), $headersUpper);
        if ($colIndex !== false) {
            $inputValue = $request->input($header);
            $data[$rowIndex][$colIndex] = $inputValue ?? '';
        } else {
            \Log::warning("Kolom header '{$header}' tidak ditemukan di spreadsheet.");
        }
    }

    // Format 2D array numerik
    $rowData = array_map(fn($val) => (string)$val, array_values($data[$rowIndex]));
    $values = [ $rowData ];

    // Hitung rentang kolom yang akan diperbarui
    $lastColIndex = count($rowData) - 1;
    $lastColLetter = $this->columnLetterFromIndex($lastColIndex);
    $updateRange = "{$this->sheetName}!A" . ($rowIndex + 1) . ":{$lastColLetter}" . ($rowIndex + 1);

    // Siapkan body update
    $body = new ValueRange([
        'range' => $updateRange,
        'majorDimension' => 'ROWS',
        'values' => $values,
    ]);

    $params = ['valueInputOption' => 'RAW'];

    // Eksekusi update ke Google Sheets
    $service->spreadsheets_values->update(
        $this->spreadsheetId,
        $updateRange,
        $body,
        $params
    );

    return redirect()->route('data.p2tl')->with('success', '✅ Data berhasil diperbarui.');
    }
}