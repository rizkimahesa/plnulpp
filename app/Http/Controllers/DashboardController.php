<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Sheets;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Setup Google Client
        $client = new Client();
        $client->setApplicationName('Laravel Google Sheets');
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig(storage_path('app/google/service-account.json'));
        $client->setAccessType('offline');

        $service = new Sheets($client);

        // Spreadsheet info
        $spreadsheetId = '1DT_HiGiBo6rfIPI1jSgI7gmZSuH2GMWc2Lj7ZOiUePY';
        $range = 'P2tl!A:AI';

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        if (!$values || count($values) === 0) {
            return view('dashboard', ['data' => []]);
        }

        // Ambil input search
        $search = $request->input('search');
        $kolom = $request->input('kolom');

        $header = $values[0];
        $dataBody = array_slice($values, 1);

        // Tampilkan hasil filter jika ada input search dan kolom
        if ($search && $kolom) {
            $colIndex = array_search($kolom, $header);
            if ($colIndex !== false) {
                $filteredData = array_filter($dataBody, function ($row) use ($colIndex, $search) {
                    return isset($row[$colIndex]) && stripos($row[$colIndex], $search) !== false;
                });
                $data = array_merge([$header], $filteredData);
            } else {
                $data = [$header]; // hanya header
            }
        } else {
            $data = [$header]; // hanya tampilkan header jika tidak ada pencarian
        }

        return view('dashboard', compact('data'));
    }
}
