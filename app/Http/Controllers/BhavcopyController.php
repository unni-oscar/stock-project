<?php

namespace App\Http\Controllers;
use App\Services\BhavcopyService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Symbol;
use App\Models\Bhavcopy;

class BhavcopyController extends Controller
{
    protected $bhavcopyService;

    public function __construct(BhavcopyService $bhavcopyService)
    {
        $this->bhavcopyService = $bhavcopyService;
    }
    public function fetchAndStoreNseData()
    {
        $nseData = $this->bhavcopyService->fetchNSEData();
        $nseRows = array_map('str_getcsv', explode("\n", $nseData));
        foreach ($nseRows as $row) {
            Bhavcopy::create([
                'symbol' => $row[0],
                'open' => $row[1],
                'high' => $row[2],
                'low' => $row[3],
                'close' => $row[4],
                'volume' => $row[5],
            ]);
        }
        return 'Data fetched and stored successfully';
    }
    public function fetchAndStoreBseData()
    {
        $bseData = $this->bhavcopyService->fetchBSEData();
        $nseRows = array_map('str_getcsv', explode("\n", $bseData));
        foreach ($nseRows as $row) {
            Bhavcopy::create([
                'symbol' => $row[0],
                'open' => $row[1],
                'high' => $row[2],
                'low' => $row[3],
                'close' => $row[4],
                'volume' => $row[5],
            ]);
        }
        return 'Data fetched and stored successfully';
    }
    public function fetchBhavcopy()
    {
        $data = [
            [
                'symbol' => '1018GS2026',
                'series' => 'GS',
                'date1' => '2024-08-16',
                'prev_close' => 124.00,
                'open_price' => 130.00,
                'high_price' => 130.00,
                'low_price' => 117.80,
                'last_price' => 117.80,
                'close_price' => 117.97,
                'avg_price' => 117.80,
                'ttl_trd_qnty' => 496,
                'turnover_lacs' => 0.59,
                'no_of_trades' => 6,
                'deliv_qty' => 494,
                'deliv_per' => 99.60
            ],
            [
                'symbol' => '20MICRONS',
                'series' => 'EQ',
                'date1' => '2024-08-16',
                'prev_close' => 293.20,
                'open_price' => 295.25,
                'high_price' => 302.00,
                'low_price' => 290.80,
                'last_price' => 296.00,
                'close_price' => 297.61,
                'avg_price' => 296.50,
                'ttl_trd_qnty' => 276858,
                'turnover_lacs' => 823.94,
                'no_of_trades' => 4932,
                'deliv_qty' => 104198,
                'deliv_per' => 37.64
            ],
            [
                'symbol' => '21STCENMGM',
                'series' => 'BE',
                'date1' => '2024-08-16',
                'prev_close' => 97.11,
                'open_price' => 97.11,
                'high_price' => 99.05,
                'low_price' => 97.11,
                'last_price' => 99.05,
                'close_price' => 97.87,
                'avg_price' => 99.05,
                'ttl_trd_qnty' => 6108,
                'turnover_lacs' => 5.98,
                'no_of_trades' => 55,
                'deliv_qty' => 580282,
                'deliv_per' => 46.77
            ],
            [
                'symbol' => '360ONE',
                'series' => 'EQ',
                'date1' => '2024-08-16',
                'prev_close' => 1026.80,
                'open_price' => 1027.00,
                'high_price' => 1089.00,
                'low_price' => 1027.00,
                'last_price' => 1070.00,
                'close_price' => 1069.60,
                'avg_price' => 1068.72,
                'ttl_trd_qnty' => 1240680,
                'turnover_lacs' => 13259.42,
                'no_of_trades' => 58676,
                'deliv_qty' => 580282,
                'deliv_per' => 46.77
            ]
        ];
        foreach ($data as $record) {
            // Check if the symbol exists or create it
            $symbol = Symbol::firstOrCreate(['symbol' => $record['symbol']]);
        
            // Add symbol_id to the record
            $record['symbol_id'] = $symbol->id;

            // Insert or update the bhavcopies record
            Bhavcopy::updateOrCreate(
                ['symbol_id' => $record['symbol_id'], 'date1' => $record['date1']],
                $record
            );
        }
        

        return 'Data stored successfully';
    }
}