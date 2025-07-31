<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Service\Sheets;
use Google\Client;

class UserDashboardController extends Controller
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
                'id' => '1Kxh6-3qWq062uyPw_LAbxMvu_7ObtH_p_xvT7Geo2ZU',
                'range' => '17120!A:CJ'
            ],
            'p2tl' => [
                'id' => '1DT_HiGiBo6rfIPI1jSgI7gmZSuH2GMWc2Lj7ZOiUePY',
                'range' => 'P2tl!A:AI'
            ],
            'harmet' => [
                'id' => '1qkDS_jlyq1jbk8oDFY9vQbSItxL-xB5B8kmfn2pvAQw',
                'range' => 'ganmet 2024 prabayar!A:Z'
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

            $header = $values[0];
            if ($key == 'harmet') {
                foreach ($header as &$h) {
                    if (strtolower(trim($h)) === 'up3') $h = 'IDPEL';
                    if (strtolower(trim($h)) === 'ulp') $h = 'Nama';
                }
            }
            $values[0] = $header;

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
                if (!$addedHeader) {
                    $finalData[] = $header;
                    $addedHeader = true;
                }
                $finalData = array_merge($finalData, array_slice($values, 1));
            }
        }

        return view('user.dashboard', ['data' => $finalData]);
    }
}
