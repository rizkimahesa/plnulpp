<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HarmetController extends Controller
{
    public function index(Request $request)
    {
        $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';
        $spreadsheetId = '1xPT7YXpXm2RwiD-Z_bbyQ-qjtE3LYxKEx6-DnqKLmYU';
        $range = '!A:M';

        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$apiKey}";
        $response = Http::get($url);

        if (!$response->successful()) {
            return view('data', ['data' => []])->withErrors("Gagal mengambil data dari Sheet: $sheetName.");
        }

        $rows = $response->json()['values'] ?? [];
        $header = $rows[0] ?? [];
        $body = array_slice($rows, 1);

        // Hapus kolom D (index ke-3)
        unset($header[3]);
        $header = array_values($header);

        $body = array_map(function ($row) {
            unset($row[3]);
            return array_values($row);
        }, $body);

        // FILTER
        $keyword = strtolower($request->input('search'));
        $filterKategori = strtolower($request->input('kategori'));
        $filterKolom = $request->input('kolom');
        $kolomIndex = array_flip(array_map('strtolower', $header));

        if ($keyword || $filterKategori || $filterKolom) {
            $body = array_filter($body, function ($row) use ($keyword, $filterKategori, $filterKolom, $kolomIndex) {
                $matchSearch = true;

                if ($keyword) {
                    if ($filterKolom && isset($kolomIndex[strtolower($filterKolom)])) {
                        $idx = $kolomIndex[strtolower($filterKolom)];
                        $matchSearch = isset($row[$idx]) && stripos($row[$idx], $keyword) !== false;
                    } else {
                        $matchSearch = false;
                        foreach ($row as $cell) {
                            if (stripos($cell, $keyword) !== false) {
                                $matchSearch = true;
                                break;
                            }
                        }
                    }
                }

                $matchKategori = true;
                if ($filterKategori && isset($kolomIndex['kategori'])) {
                    $idx = $kolomIndex['kategori'];
                    $matchKategori = isset($row[$idx]) && strtolower($row[$idx]) === $filterKategori;
                }

                return $matchSearch && $matchKategori;
            });
        }

        $data = [$header, ...$body];

        return view('data', compact('data'));
    }
}
