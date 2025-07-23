<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Google\Service\Sheets;

class P2tlController extends Controller
{
    protected $spreadsheetId = '1qOjBDKZ6ZIvrP_otBfqXc9EA1PfBjzhPl0wBwc9Fk5M';
    protected $sheetName = 'Sheet1';
    protected $apiKey = 'AIzaSyCz5r5jRyKdrnpx1v-w8fzrJ4OEQphBIm4';

    // Tampilkan tabel
    public function index()
    {
        $response = Http::get("https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$this->sheetName}?key={$this->apiKey}");
        $data = $response->json()['values'] ?? [];

        return view('data', ['data' => $data]);
    }
}