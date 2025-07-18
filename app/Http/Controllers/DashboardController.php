<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Service\Sheets;
use Google\Client;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori');
        $kolom = $request->input('kolom');

        $client = new Client();
        $client->setApplicationName('Laravel Google Sheets');
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig(storage_path('app/credentials.json'));
        $service = new Sheets($client);

        $sheetConfigs = [
            'billing' => [
                'id' => '1a5DSLnWj6WWPkJZzoCgb4ggvyONzFFo_LHA1lTYyam0',
                'range' => 'Sheet1!A:Z'
            ],
            'p2tl' => [
                'id' => '1qOjBDKZ6ZIvrP_otBfqXc9EA1PfBjzhPl0wBwc9Fk5M',
                'range' => 'Sheet1!A:Z'
            ],
            'harmet' => [
                'id' => '1xPT7YXpXm2RwiD-Z_bbyQ-qjtE3LYxKEx6-DnqKLmYU',
                'range' => 'Sheet1!A:Z'
            ],
        ];

        $selectedSheets = [];

        if ($kategori && isset($sheetConfigs[$kategori])) {
            $selectedSheets[$kategori] = $sheetConfigs[$kategori];
        } else {
            $selectedSheets = $sheetConfigs;
        }

        $finalData = [];
        $addedHeader = false;

        foreach ($selectedSheets as $key => $sheet) {
            $response = $service->spreadsheets_values->get($sheet['id'], $sheet['range']);
            $values = $response->getValues();

            if (empty($values)) continue;

            // Header normalization
            $header = $values[0];
            if ($key == 'harmet') {
                foreach ($header as &$h) {
                    if (strtolower(trim($h)) === 'up3') $h = 'Idpel';
                    if (strtolower(trim($h)) === 'ulp') $h = 'Nama';
                }
            }
            $values[0] = $header;

            // Pencarian jika search dan kolom diisi
            if ($search && $kolom) {
                $colIndex = array_search($kolom, $header);
                if ($colIndex !== false) {
                    $matchedRows = array_filter(array_slice($values, 1), function ($row) use ($colIndex, $search) {
                        return isset($row[$colIndex]) && stripos($row[$colIndex], $search) !== false;
                    });

                    if (!empty($matchedRows)) {
                        if (!$addedHeader) {
                            $finalData[] = $header;
                            $addedHeader = true;
                        }
                        $finalData = array_merge($finalData, $matchedRows);
                    }
                }
            } else {
                // Jika tidak sedang mencari, gabungkan semua data
                if (!$addedHeader) {
                    $finalData[] = $header;
                    $addedHeader = true;
                }
                $finalData = array_merge($finalData, array_slice($values, 1));
            }
        }

        return view('dashboard', ['data' => $finalData]);
    }
}
