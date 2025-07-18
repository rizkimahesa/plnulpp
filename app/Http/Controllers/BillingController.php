<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';
        $spreadsheetId = '1a5DSLnWj6WWPkJZzoCgb4ggvyONzFFo_LHA1lTYyam0';
        $range ='!A:CJ';

        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$apiKey}";
        $response = Http::get($url);

        if (!$response->successful()) {
            return view('data', ['data' => []])->withErrors("Gagal mengambil data dari Sheet: $sheetName.");
        }

        $rows = $response->json()['values'] ?? [];
        $header = $rows[0] ?? [];
        $body = array_slice($rows, 1);

        // Buat array asosiatif
        $mappedBody = array_map(function ($row) use ($header) {
            $assoc = [];
            foreach ($header as $i => $key) {
                $assoc[$key] = $row[$i] ?? '';
            }
            return $assoc;
        }, $body);

        // Filtering
        $keyword = strtolower($request->input('search'));
        $filterKategori = strtolower($request->input('kategori'));
        $filterKolom = $request->input('kolom');
        $kolomIndex = array_flip(array_map('strtolower', $header));

        if ($keyword || $filterKategori || $filterKolom) {
            $mappedBody = array_filter($mappedBody, function ($row) use ($keyword, $filterKategori, $filterKolom) {
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
                if ($filterKategori && isset($row['kategori'])) {
                    $matchKategori = strtolower($row['kategori']) === $filterKategori;
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
}