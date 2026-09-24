<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BillingController extends Controller
{
    public function index(Request $request)
{

    $range ='Sheet1!A:CJ';

    $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$apiKey}";
    $response = Http::get($url);

    if (!$response->successful()) {
        return view('data', ['data' => []])->withErrors("Gagal mengambil data.");
    }

    $rows = $response->json()['values'] ?? [];
    $header = $rows[0] ?? [];
    $body = array_slice($rows, 1);

    $keyword = strtolower($request->input('search'));
    $filterKategori = strtolower($request->input('kategori'));
    $filterKolom = $request->input('kolom');
    $kolomIndex = array_flip(array_map('strtolower', $header));

    if ($keyword || $filterKategori || $filterKolom) {
        $body = array_filter($body, function ($row) use ($keyword, $filterKategori, $filterKolom, $header, $kolomIndex) {
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
            if ($filterKategori) {
                $kategoriIdx = $kolomIndex['kategori'] ?? null;
                $matchKategori = $kategoriIdx !== null && isset($row[$kategoriIdx]) && strtolower($row[$kategoriIdx]) === $filterKategori;
            }

            $matchKolom = true;
            if ($filterKolom) {
                $colIdx = $kolomIndex[strtolower($filterKolom)] ?? null;
                $matchKolom = $colIdx !== null && isset($row[$colIdx]) && stripos($row[$colIdx], $keyword) !== false;
            }

            return $matchSearch && $matchKategori && $matchKolom;
        });
    }

    $data = [$header, ...array_values($body)];

    return view('data', compact('data'));
    }
}
