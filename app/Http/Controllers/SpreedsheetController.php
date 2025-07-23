<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use App\Services\GoogleDriveService;


class SpreedsheetController extends Controller
{
    private $spreadsheetId = '1qOjBDKZ6ZIvrP_otBfqXc9EA1PfBjzhPl0wBwc9Fk5M';
    private $sheetName = 'Sheet1';
    private $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';

    protected $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }


    private function getSheetData($spreadsheetId, $range)
    {
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$this->apiKey}";
        $response = Http::get($url);
        return $response->json()['values'] ?? [];
    }

    private function initGoogleClient($scopes = [Sheets::SPREADSHEETS])
    {
        $client = new Client();
        $client->setApplicationName('Laravel Google Sheets');
        $client->setScopes($scopes);
        $client->setAuthConfig(storage_path('app/credentials.json'));
        return new Sheets($client);
    }

    private function findRowByIdpel($data, $idpelIndex, $id)
    {
        foreach ($data as $i => $row) {
            if (isset($row[$idpelIndex]) && $row[$idpelIndex] == $id) {
                return $i;
            }
        }
        return null;
    }

    private function columnLetterFromIndex($index)
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intval($index / 26) - 1;
        }
        return $letter;
    }

    public function data(Request $request)
    {
        $range = 'Sheet1!A:AI';
        $rows = $this->getSheetData($this->spreadsheetId, $range);
        $header = $rows[0] ?? [];
        $body = array_slice($rows, 1);
        $index = array_flip(array_map('strtolower', $header));

        $keyword = strtolower($request->input('search'));
        $kategori = strtolower($request->input('kategori'));
        $kolom = strtolower(trim($request->input('kolom')));

        if ($keyword || $kategori || $kolom) {
            $body = array_filter($body, function ($row) use ($keyword, $kategori, $kolom, $index) {
                $matchSearch = !$keyword || array_filter($row, fn($cell) => stripos($cell, $keyword) !== false);
                $matchKategori = !$kategori || (isset($index['kategori']) && strtolower($row[$index['kategori']] ?? '') === $kategori);
                $matchKolom = !$kolom || (isset($index[strtolower($kolom)]) && stripos($row[$index[strtolower($kolom)]] ?? '', $keyword) !== false);
                return $matchSearch && $matchKategori && $matchKolom;
            });
        }

        $idpelLunas = array_column(array_filter($body, fn($row) => strtolower($row[$index['status']] ?? '') === 'lunas'), $index['idpel']);
        $realisasiData = [];

        if (!empty($idpelLunas)) {
            // Ambil data realisasi
            $realRows = $this->getSheetData('1_gtHDcSetTEggCVeLt1H_nx_25rXXOrvM0BMWa6plfE', 'Sheet1!A:AI');
            $realHeader = $realRows[0] ?? [];
            $realBody = array_slice($realRows, 1);
            $realIndex = array_flip(array_map('strtolower', $realHeader));

            // Normalisasi semua IDPEL dari p2tl
            $normalizedIdpelLunas = array_map(function ($val) {
                return strtolower(trim((string)$val));
            }, $idpelLunas);

            if (isset($realIndex['idpel'])) {
                $filteredReal = array_filter($realBody, function ($row) use ($realIndex, $normalizedIdpelLunas) {
                    $idpelReal = strtolower(trim((string)($row[$realIndex['idpel']] ?? '')));
                    return in_array($idpelReal, $normalizedIdpelLunas);
                });

                $realisasiData = [$realHeader, ...array_values($filteredReal)];
            }
        }

        return view('data', [
            'data' => [$header, ...array_values($body)],
            'realisasiData' => $realisasiData,
        ]);
    }

    public function edit($id)
    {
        $service = $this->initGoogleClient([Sheets::SPREADSHEETS_READONLY]);
        $range = $this->sheetName . '!A:AI';
        $values = $service->spreadsheets_values->get($this->spreadsheetId, $range)->getValues();

        $headers = $values[0];
        $idpelIndex = array_search('IDPEL', $headers);
        $rowToEdit = collect(array_slice($values, 1))->firstWhere($idpelIndex, $id);

        if (!$rowToEdit) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Ambil link folder Google Drive berdasarkan tanggal hari ini
        $parentFolderId = '15_XJRPQ15ErknO4h7eNDX1ciV-JoRvlx';
        $todayFormatted = now()->format('d-m-Y');
        $folderToday = $this->driveService->findFolderByDate($parentFolderId, $todayFormatted);
        $linkDriveHariIni = $folderToday ? "https://drive.google.com/drive/folders/" . $folderToday['id'] : null;

        return view('user.edit', [
            'id' => $id,
            'headers' => $headers,
            'row' => $rowToEdit,
            'linkDriveHariIni' => $linkDriveHariIni,
        ]);
    }


    public function update(Request $request, $id)
{
    $service = $this->initGoogleClient();
    $range = $this->sheetName . '!A:AI';
    $data = $service->spreadsheets_values->get($this->spreadsheetId, $range)->getValues();

    $headers = $data[0];
    $headersUpper = array_map('strtoupper', $headers);
    $idpelIndex = array_search('IDPEL', $headersUpper);
    $rowIndex = $this->findRowByIdpel($data, $idpelIndex, $id);

    if (is_null($rowIndex)) {
        return redirect()->route('data.p2tl')->with('error', 'ID tidak ditemukan dalam spreadsheet.');
    }

    // Index kolom yang bisa diedit (R sampai Z = 17-25)
    $editableIndexes = [17, 18, 19, 20, 21, 22, 23, 24, 25];
    $data[$rowIndex] = array_pad($data[$rowIndex], 35, '');

    foreach ($editableIndexes as $colIndex) {
        $inputKey = 'col_' . $colIndex;
        if ($request->has($inputKey)) {
            $data[$rowIndex][$colIndex] = $request->input($inputKey, '');
        }
    }

    $maxCols = max(count($headers), 35);
    $data[$rowIndex] = array_pad($data[$rowIndex], $maxCols, '');

    $values = [ array_map('strval', $data[$rowIndex]) ];
    $lastCol = $this->columnLetterFromIndex(count($values[0]) - 1);
    $updateRange = "{$this->sheetName}!A" . ($rowIndex + 1) . ":{$lastCol}" . ($rowIndex + 1);

    $body = new ValueRange([
        'range' => $updateRange,
        'majorDimension' => 'ROWS',
        'values' => $values,
    ]);

    $service->spreadsheets_values->update(
        $this->spreadsheetId,
        $updateRange,
        $body,
        ['valueInputOption' => 'RAW']
    );

    return redirect()->route('data.p2tl')->with('success', '✅ Data berhasil diperbarui.');
    }

    public function realisasiByIdpel($idpel)
{
    try {
        $spreadsheetId = '1_gtHDcSetTEggCVeLt1H_nx_25rXXOrvM0BMWa6plfE';
        $range = 'Sheet1!A:AI';

        $rows = $this->getSheetData($spreadsheetId, $range);
        if (count($rows) < 4) {
            throw new \Exception("Data tidak ditemukan.");
        }

        $header = $rows[2]; // Baris ke-4 (index 2)
        $body = array_slice($rows, 6);

        // Buat map header UPPERCASE
        $indexMap = array_flip(array_map('strtoupper', $header));
        if (!isset($indexMap['IDPEL'])) {
            throw new \Exception("Kolom IDPEL tidak ditemukan.");
        }

        $filtered = array_filter($body, function ($row) use ($indexMap, $idpel) {
            return isset($row[$indexMap['IDPEL']]) &&
                   strtoupper(trim($row[$indexMap['IDPEL']])) === strtoupper(trim($idpel));
        });

        if (empty($filtered)) {
            throw new \Exception("Data dengan IDPEL '$idpel' tidak ditemukan.");
        }

        $data = [$header, ...array_values($filtered)];
        return view('data', compact('data'));

    } catch (\Exception $e) {
        return view('data', [
            'data' => [],
            'error' => $e->getMessage()
        ]);
        }
    }
}
