<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RealisasiController extends Controller
{
    public function index(Request $request)
    {
        return $this->fetchFromSheet($request, 'realisasi');
    }

    private function fetchFromSheet(Request $request, $sheetName)
    {
        $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';
        $spreadsheetId = '1_gtHDcSetTEggCVeLt1H_nx_25rXXOrvM0BMWa6plfE';
        $range = '!A:AI';

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
    $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';
    $spreadsheetId = '1_gtHDcSetTEggCVeLt1H_nx_25rXXOrvM0BMWa6plfE';
    $range = '!A:AI';

    $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$apiKey}";
    $response = Http::get($url);

    if (!$response->successful()) {
        return view('data', ['data' => []])->withErrors('Gagal mengambil data realisasi.');
    }

    $rows = $response->json()['values'] ?? [];
    $header = $rows[0] ?? [];
    $body = array_slice($rows, 1);
    $indexMap = array_flip(array_map('strtolower', $header));

    $filtered = array_filter($body, function ($row) use ($indexMap, $idpel) {
        return isset($indexMap['idpel']) && isset($row[$indexMap['idpel']]) && $row[$indexMap['idpel']] === $idpel;
    });

    $data = [$header, ...$filtered];

    return view('data', compact('data'));
    }
}
