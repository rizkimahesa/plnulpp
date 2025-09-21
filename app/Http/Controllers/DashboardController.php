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
                'range' => 'Sheet1!A:AI'
            ],
            'harmet' => [
                'id' => '1xPT7YXpXm2RwiD-Z_bbyQ-qjtE3LYxKEx6-DnqKLmYU',
                'range' => 'Sheet1!A:Z'
            ],
            'pem kwh' => [
                'id' => '1KqZ6YrNpZURqc3IlH3_JOLtaZ3DvUXbh2mhhLderdrs',
                'range' => 'ALL_SHEET'
            ],
        ];

        $selectedSheets = $kategori && isset($sheetConfigs[$kategori])
            ? [$kategori => $sheetConfigs[$kategori]]
            : $sheetConfigs;

        $finalData = [];
        $chartData = [];
        $addedHeader = false;

        foreach ($selectedSheets as $key => $sheet) {
            if ($key === 'pem kwh') {
                $spreadsheet = $service->spreadsheets->get($sheet['id']);
                $sheetTitles = array_map(fn($s) => $s->getProperties()->getTitle(), $spreadsheet->getSheets());

                foreach ($sheetTitles as $title) {
                    $range = $title . '!A:N';
                    $response = $service->spreadsheets_values->get($sheet['id'], $range);
                    $values = $response->getValues();

                    if (empty($values)) continue;

                    $header = $values[0];
                    $body = array_slice($values, 1);

                    // Untuk pencarian
                    if ($search && $kolom) {
                        $colIndex = array_search($kolom, $header);
                        if ($colIndex !== false) {
                            $matchedRows = array_filter($body, fn($row) =>
                                isset($row[$colIndex]) && stripos($row[$colIndex], $search) !== false
                            );

                            if (!empty($matchedRows)) {
                                if (!$addedHeader) {
                                    $finalData[] = $header;
                                    $addedHeader = true;
                                }
                                $finalData = array_merge($finalData, $matchedRows);
                            }

                            // Ambil data grafik PEMKWH
                            $colIdpel = array_search('IDPEL', array_map('strtoupper', $header));
                            $colNama = array_search('NAMA', array_map('strtoupper', $header));
                            $colPemkwh = array_search('PEMKWH', array_map('strtoupper', $header));

                            foreach ($matchedRows as $row) {
                                $match = false;

                                // Cek berdasarkan IDPEL
                                if (strtolower($kolom) === 'idpel' && $colIdpel !== false) {
                                    if (isset($row[$colIdpel]) && stripos($row[$colIdpel], $search) !== false) {
                                        $match = true;
                                    }
                                }

                                // Cek berdasarkan NAMA
                                if (strtolower($kolom) === 'nama' && $colNama !== false) {
                                    if (isset($row[$colNama]) && stripos($row[$colNama], $search) !== false) {
                                        $match = true;
                                    }
                                }

                                // Kalau cocok, tambahkan ke chartData
                                if ($match && $colPemkwh !== false) {
                                    $chartData[] = [
                                        'label' => $title, // Nama sheet = bulan
                                        'value' => isset($row[$colPemkwh]) ? floatval($row[$colPemkwh]) : 0,
                                    ];
                                }
                            }
                        }
                    } else {
                        if (!$addedHeader) {
                            $finalData[] = $header;
                            $addedHeader = true;
                        }
                        $finalData = array_merge($finalData, $body);
                    }
                }
            } else {
                $response = $service->spreadsheets_values->get($sheet['id'], $sheet['range']);
                $values = $response->getValues();

                if (empty($values)) continue;

                $header = $values[0];
                if ($key === 'harmet') {
                    foreach ($header as &$h) {
                        if (strtolower(trim($h)) === 'up3') $h = 'Idpel';
                        if (strtolower(trim($h)) === 'ulp') $h = 'Nama';
                    }
                }

                $values[0] = $header;

                if ($search && $kolom) {
                    $colIndex = array_search($kolom, $header);
                    if ($colIndex !== false) {
                        $matchedRows = array_filter(array_slice($values, 1), fn($row) =>
                            isset($row[$colIndex]) && stripos($row[$colIndex], $search) !== false
                        );

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
        }

        return view('dashboard', [
            'data' => $finalData,
            'chartData' => $chartData
        ]);
    }
}
