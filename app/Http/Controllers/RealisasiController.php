<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RealisasiController extends Controller
{
    public function index(Request $request)
    {
        return $this->fetchFromSheet($request, 'REALISASI');
    }

    private function fetchFromSheet(Request $request, $sheetName)
    {
        $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';
        $spreadsheetId = '1DT_HiGiBo6rfIPI1jSgI7gmZSuH2GMWc2Lj7ZOiUePY';
        $range = 'REALISASI!A:AI';

        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$apiKey}";
        $response = Http::get($url);

        if (!$response->successful()) {
            return view('data', ['data' => []])->withErrors("Gagal mengambil data dari Sheet: $sheetName.");
        }

        $rows = $response->json()['values'] ?? [];
        $header = $rows[0] ?? [];
        $body = array_slice($rows, 1);

        $keyword = strtolower($request->input('search'));
        $filterKategori = strtolower($request->input('kategori'));
        $filterKolom = $request->input('kolom');
        $kolomIndex = array_flip(array_map('strtolower', $header));

        if ($keyword || $filterKategori || $filterKolom) {
            $body = array_filter($body, function ($row) use ($keyword, $filterKategori, $filterKolom, $kolomIndex) {
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
                if ($filterKategori && isset($kolomIndex['kategori'])) {
                    $idx = $kolomIndex['kategori'];
                    $matchKategori = isset($row[$idx]) && strtolower($row[$idx]) === $filterKategori;
                }

                $matchKolom = true;
                if ($filterKolom && isset($kolomIndex[strtolower($filterKolom)])) {
                    $idx = $kolomIndex[strtolower($filterKolom)];
                    $matchKolom = isset($row[$idx]) && stripos($row[$idx], $keyword) !== false;
                }

                return $matchSearch && $matchKategori && $matchKolom;
            });
        }

        $data = [$header, ...$body];

        return view('data', compact('data'));
    }

    public function byIdpel($idpel)
{
    try {
        $spreadsheetId = '1DT_HiGiBo6rfIPI1jSgI7gmZSuH2GMWc2Lj7ZOiUePY';
        $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';
        $range = 'REALISASI!A:AI';

        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$apiKey}";
        $response = Http::get($url);

        if (!$response->successful()) {
            throw new \Exception('Gagal mengambil data dari Google Sheets.');
        }

        $values = $response->json('values');
        if (empty($values)) {
            throw new \Exception('Data Google Sheets kosong.');
        }

        // Baris ke-3 dianggap sebagai header
        $header = $values[0];
        $body = array_slice($values, 5);

        // Mapping header (gunakan UPPERCASE agar seragam)
        $indexMap = [];
        foreach ($header as $index => $column) {
            $indexMap[strtoupper(trim($column))] = $index;
        }

        if (!isset($indexMap['IDPEL'])) {
            throw new \Exception('Kolom IDPEL tidak ditemukan di header.');
        }

        // Pencarian berdasarkan IDPEL (case-insensitive)
        $index = $indexMap['IDPEL'];
        $filtered = array_filter($body, function ($row) use ($index, $idpel) {
            $idpelRow = strtoupper(trim($row[$index] ?? ''));
            return $idpelRow === strtoupper(trim($idpel));
        });

        if (empty($filtered)) {
            throw new \Exception("Data dengan IDPEL '$idpel' tidak ditemukan.");
        }

        $data = [$header, ...$filtered];
        return view('data', compact('data'));

    } catch (\Exception $e) {
        return response()->view('data', [
            'data' => [],
            'error' => $e->getMessage()
        ], 500);
    }
}
}