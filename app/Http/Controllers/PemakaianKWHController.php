<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PemakaianKWHController extends Controller
{
    public function index(Request $request)
    {
        $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';
        $spreadsheetId = '1rV6_c94BlomYbWamH0zTWay9l5Bad3DBcl59dhC8EYQ';

        // Ambil semua nama sheet
        $sheetMetaUrl = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}?key={$apiKey}";
        $metaResponse = Http::get($sheetMetaUrl);
        $sheetNames = [];

        if ($metaResponse->successful()) {
            $sheets = $metaResponse->json()['sheets'] ?? [];
            foreach ($sheets as $sheet) {
                $sheetNames[] = $sheet['properties']['title'];
            }
        }

        // Ambil nama bulan dari query
        $bulan = $request->input('bulan') ?? $sheetNames[0] ?? '';
        $range = $bulan . '!A:N';
        $url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/{$range}?key={$apiKey}";
        $response = Http::get($url);

        $rows = $response->successful() ? ($response->json()['values'] ?? []) : [];
        $header = $rows[0] ?? [];
        $body = array_slice($rows, 1);

        // 🔍 Logika Search
        $keyword = strtolower($request->input('search'));
        $column = $request->input('column');

        if ($keyword && $column && in_array($column, $header)) {
            $colIndex = array_search($column, $header);
            $body = array_filter($body, function ($row) use ($colIndex, $keyword) {
                return isset($row[$colIndex]) && str_contains(strtolower($row[$colIndex]), $keyword);
            });
        }

        return view('data-pemakaian', [
            'data' => $body,
            'header' => $header,
            'sheetNames' => $sheetNames,
            'bulan' => $bulan,
            'search' => $request->input('search'),
            'column' => $column
        ]);
    }
}
