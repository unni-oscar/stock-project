<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Bhavcopy;

class BhavcopyController extends Controller
{
    public function fetchBhavcopy()
    {
        // $response = file_get_contents('https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_14082024.csv');
        // if ($response === FALSE) {
        //     die('Error fetching data');
        // }
        $client = new Client();
        $response = $client->request('GET', 'https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_14082024.csv', [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
            ]
        ]);

        // $client = new Client();
        // $response = $client->request('GET',
        //  'https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_14082024.csv');
        $body = $response->getBody();
        $csv = $body->getContents();

        // Process the CSV and insert into the database
        $rows = explode("\n", $csv);
        foreach ($rows as $row) {
            $data = str_getcsv($row);
            if (!empty($data)) {
                Bhavcopy::create([
                    'symbol' => $data[0],
                    'series' => $data[1],
                    'open' => $data[2],
                    'high' => $data[3],
                    'low' => $data[4],
                    'close' => $data[5],
                    'last' => $data[6],
                    'prev_close' => $data[7],
                    'tot_trd_qt' => $data[8],
                    'tot_trd_val' => $data[9],
                    'timestamp' => $data[10],
                ]);
            }
        }

        return 'Bhavcopy data inserted successfully.';
    }
}