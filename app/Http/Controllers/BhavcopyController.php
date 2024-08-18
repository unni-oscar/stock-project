<?php

namespace App\Http\Controllers;
use App\Services\BhavcopyService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Symbol;
use App\Models\Bhavcopy;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;



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


    function downloadFile($url, $localPath)
    {
        // Check if the file already exists
        if (file_exists($localPath)) {
            Log::info("File already exists: $localPath");
            return;
        }

        // Open a file pointer to the URL
        $fp = fopen($url, 'rb');
        if ($fp === false) {
            Log::error("Failed to open URL: $url");
            return;
        }

        // Open a local file for writing
        $localFile = fopen($localPath, 'wb');
        if ($localFile === false) {
            fclose($fp);
            Log::error("Failed to open local file for writing: $localPath");
            return;
        }

        // Write the file from the URL to the local file
        while (!feof($fp)) {
            $chunk = fread($fp, 8192);
            fwrite($localFile, $chunk);
        }

        // Close the file pointers
        fclose($fp);
        fclose($localFile);

        Log::info("File downloaded successfully: $localPath");
    }

    public function showReport()
    {
          // Get all symbols
        // $symbols = Symbol::all();
        $symbols = Symbol::where('symbol', '20MICRONS')->get();

        $symbolDetails = $symbols->map(function ($symbol) {
            
            
            $latestRecord = Bhavcopy::where('symbol_id', $symbol->id)
                ->where('series', 'EQ')
                ->orderBy('date1', 'desc')
                 ->first(); // Correct method to get the record
                
                      if ($latestRecord) {
                $latestDate = Carbon::parse($latestRecord->date1);

                // Get deliv_per data
                $delivPerData = Bhavcopy::where('symbol_id', $symbol->id)
                    ->where('series', 'EQ')
                    ->whereDate('date1', '<=', $latestDate)
                    ->orderBy('date1', 'desc')
                    ->get(['deliv_per', 'date1']);

                Log::info('Fetched deliv_per data:', $delivPerData->toArray());

                    
                // Calculate averages
                $threeDayAvg = $this->calculateAverage($delivPerData, 4);
                // $fiveDayAvg = $this->calculateAverage($delivPerData, 5);
                // $thirtyDayAvg = $this->calculateAverage($delivPerData, 30);

                return [
                    'symbol' => $symbol->symbol,
                    'latest_deliv_per' => $latestRecord->deliv_per,
                    'three_day_avg' => $threeDayAvg,
                    // 'five_day_avg' => $fiveDayAvg,
                    // 'thirty_day_avg' => $thirtyDayAvg,
                ];
            } else {
                return [
                    'symbol' => $symbol->symbol,
                    'latest_deliv_per' => null,
                    'three_day_avg' => null,
                    // 'five_day_avg' => null,
                    // 'thirty_day_avg' => null,
                ];
            }
        });
      
        // Sort by latest_deliv_per in descending order
        $symbolDetails = $symbolDetails->sortByDesc('latest_deliv_per');

        // return view('symbol_details', compact('symbolDetails'));
        return view('bhavcopy_report', compact('symbolDetails'));
    }

    private function calculateAverage($records, $days)
    {

        // $dateCutoff = Carbon::now()->subDays($days);
        // $filteredRecords = $records->filter(function ($record) use ($dateCutoff) {
        //     return Carbon::parse($record->date1)->gte($dateCutoff);
        // });
        // if ($filteredRecords->isEmpty()) {
        //     return null;
        // }

        // $total = $filteredRecords->sum('deliv_per');
      
        // return $total / $filteredRecords->count();

        // Log initial state
        Log::info('Calculating average with days:', ['days' => $days]);

        // Get the date cutoff as the start of the day
        $dateCutoff = Carbon::now()->subDays($days)->startOfDay();
        Log::info('Date Cutoff:', ['dateCutoff' => $dateCutoff]);

        // Filter records
        $filteredRecords = $records->filter(function ($record) use ($dateCutoff) {
            // Ensure date1 is parsed correctly
            $recordDate = Carbon::parse($record->date1)->startOfDay();
            Log::info('Record Date:', ['date' => $recordDate->format('Y-m-d H:i:s')]);
Log::info('Date Cutoff:', ['dateCutoff' => $dateCutoff->format('Y-m-d H:i:s')]);


            Log::info('Filtering record:', ['date' => $recordDate, 'cutoff' => $dateCutoff]);
            // Log each record being checked
        Log::info('Filtering record:', [
            'date' => $recordDate,
            'cutoff' => $dateCutoff,
            'result' => $recordDate->gte($dateCutoff)
        ]);
            return $recordDate->gte($dateCutoff);
        });

        Log::info('Filtered records:', $filteredRecords->toArray());

        if ($filteredRecords->isEmpty()) {
            Log::info('No records to calculate average.');
            return null;
        }

        // $total = $filteredRecords->sum('deliv_per');
        $total = $filteredRecords->sum(function($record) {
            return (float)$record->deliv_per;
        });
        $count = $filteredRecords->count();
        Log::info('Total deliv_per:', ['total' => $total, 'count' => $count]);

        return $total / $count;
    }

    public function fetchBhavcopy()
    {
        $isFileDownloaded  = $this->bhavcopyService->fetchNSEData('12082024');

        //error 10082024  03082024  17082024
        // dd($isFileDownloaded);
        if($isFileDownloaded['success']) {
            $insertStatus = $this->bhavcopyService->processNseBhavcopy($isFileDownloaded);
            return $insertStatus['message'];
        } else {
            return [
                'success' => false,
                'message' => 'Cannot find bhavcopy'
            ];
        }
        
        
        
        
        // $url = config('stock.url');
        echo '$res';
    //    echo  $this->bhavcopyService->process('11061978');

            // docker composer exec web php artisan bhavcopy:process



        // // Access the configuration values
        // $apiKey = config('custom.api_key');
        // $siteUrl = config('custom.site_url');

        // // Use the values as needed
        // return response()->json([
        //     'api_key' => $apiKey,
        //     'site_url' => $siteUrl,
        // ]);
       //---working
        // $ch = curl_init('https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_16082024.csv');
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

       
        // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //     'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        // ]);

        // $response = curl_exec($ch);
        // if ($response === false) {
        //     echo 'cURL Error: ' . curl_error($ch);
        // } else {
        //     $directory = storage_path('app/bhavcopies');
        //     $filePath = $directory . '/sec_bhavdata_full_16082024.csv';

        //     if (!file_exists($directory)) {
        //         mkdir($directory, 0755, true); // Create directory if it does not exist
        //     }

        //     $localPath = 'storage/app/bhavcopies/sec_bhavdata_full_16082024.csv';
        //     file_put_contents($filePath, $response);
        // }

        // curl_close($ch);


            // -----

    //     $response = Http::get('https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_16082024.csv');
    //      $csvData = $response->body();

    // // Save the CSV data to a temporary file
    //         Storage::disk('local')->put('temp.csv', $csvData);

        // $response = Http::get('https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_16082024.csv');
        // $data = $response->body();

        // $rows = explode("\n", $data);
        // foreach ($rows as $row) {
        //     $columns = explode(",", $row);
        //     echo $columns[0];
        //     if (count($columns) == 12) {
        //         // SecBhavdata::create([
        //         //     'SYMBOL' => $columns[0],
        //         //     'SERIES' => $columns[1],
        //         //     'OPEN' => $columns[2],
        //         //     'HIGH' => $columns[3],
        //         //     'LOW' => $columns[4],
        //         //     'CLOSE' => $columns[5],
        //         //     'LAST' => $columns[6],
        //         //     'PREVCLOSE' => $columns[7],
        //         //     'TOTTRDQTY' => $columns[8],
        //         //     'TOTTRDVAL' => $columns[9],
        //         //     'TIMESTAMP' => $columns[10],
        //         // ]);
        //     }
        // }

        // return response()->json(['message' => 'Data stored successfully']);
        // try {
        //     $url = 'https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_16082024.csv';
    
        //     // Fetch the contents using cURL
        //     $ch = curl_init($url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // Use HTTP/1.1
        //     curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Set timeout to 60 seconds
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for testing
        //     $fileContents = curl_exec($ch);
    
        //     if ($fileContents === false) {
        //         throw new \Exception('cURL Error: ' . curl_error($ch));
        //     }
    
        //     curl_close($ch);
    
        //     // Convert CSV string into an array
        //     $rows = array_map("str_getcsv", explode("\n", $fileContents));
    
        //     // Remove the header row if it exists
        //     $header = array_shift($rows);
    
        //     // Prepare batch insert
        //     $batch = [];
        //     $batchSize = 500;
    
        //     foreach ($rows as $data) {
        //         if (!empty($data) && is_array($data)) {
        //             echo $data[0];
        //             $batch[] = [
        //                 'column1' => $data[0] ?? null, // Replace with actual column name
        //                 'column2' => $data[1] ?? null, // Replace with actual column name
        //                 // Add other columns as needed
        //             ];
    
        //             if (count($batch) >= $batchSize) {
        //              //   DB::table('external_data')->insert($batch);
        //                 $batch = [];
        //             }
        //         }
        //     }
    
        //     if (!empty($batch)) {
        //        // DB::table('external_data')->insert($batch);
        //     }
    
        //     return response()->json(['success' => 'Data imported successfully.']);
    
        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Error occurred: ' . $e->getMessage()], 500);
        // }
        // try {
        //     $url = 'https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_16082024.csv';
    
        //     // Fetch the contents from the URL
        //     $fileContents = file_get_contents($url);
    
        //     if ($fileContents === false) {
        //         throw new \Exception("Failed to download file from URL: $url");
        //     }
    
        //     // Convert CSV string into an array
        //     $rows = array_map("str_getcsv", explode("\n", $fileContents));
    
        //     // Remove the header row if it exists
        //     $header = array_shift($rows);
    
        //     // Prepare batch insert
        //     $batch = [];
        //     $batchSize = 500;
    
        //     foreach ($rows as $data) {
        //         if (!empty($data)) {
        //             echo $data[0];
        //             $batch[] = [
        //                 'column1' => $data[0] ?? null, // Replace with actual column name
        //                 'column2' => $data[1] ?? null, // Replace with actual column name
        //                 // Add other columns as needed
        //             ];
    
        //             if (count($batch) >= $batchSize) {
        //                // DB::table('external_data')->insert($batch);
        //                 $batch = [];
        //             }
        //         }
        //     }
    
        //     if (!empty($batch)) {
        //         //DB::table('external_data')->insert($batch);
        //     }
    
        //     return response()->json(['success' => 'Data imported successfully.']);
    
        // } catch (\Exception $e) {
        //     return response()->json(['error' => 'Error occurred: ' . $e->getMessage()], 500);
        // }



        // $url = 'https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_16082024.csv';
        // $localPath = 'storage/app/bhavcopies/sec_bhavdata_full_16082024.csv';

        // // Call the function to download the file
        // $this->downloadFile($url, $localPath);
        // $client = new Client();
        //  try {
        //     $response = $client->get('https://nsearchives.nseindia.com/products/content/sec_bhavdata_full_16082024.csv', [
        //         'sink' => storage_path('app/bhavcopies/sec_bhavdata_full_16082024.csv'),
        //         'timeout' => 60, // Increase timeout if necessary
        //     ]);
        
        //     if ($response->getStatusCode() == 200) {
        //         Log::info('File downloaded successfully.');
        //     } else {
        //         Log::error('Failed to download file. Status code: ' . $response->getStatusCode());
        //     }
        // } catch (\Exception $e) {
        //     Log::error('Error occurred while downloading the file: ' . $e->getMessage());
        // }
        // $data = [
        //     [
        //         'symbol' => '1018GS2026',
        //         'series' => 'GS',
        //         'date1' => '2024-08-16',
        //         'prev_close' => 124.00,
        //         'open_price' => 130.00,
        //         'high_price' => 130.00,
        //         'low_price' => 117.80,
        //         'last_price' => 117.80,
        //         'close_price' => 117.97,
        //         'avg_price' => 117.80,
        //         'ttl_trd_qnty' => 496,
        //         'turnover_lacs' => 0.59,
        //         'no_of_trades' => 6,
        //         'deliv_qty' => 494,
        //         'deliv_per' => 99.60
        //     ],
        //     [
        //         'symbol' => '20MICRONS',
        //         'series' => 'EQ',
        //         'date1' => '2024-08-16',
        //         'prev_close' => 293.20,
        //         'open_price' => 295.25,
        //         'high_price' => 302.00,
        //         'low_price' => 290.80,
        //         'last_price' => 296.00,
        //         'close_price' => 297.61,
        //         'avg_price' => 296.50,
        //         'ttl_trd_qnty' => 276858,
        //         'turnover_lacs' => 823.94,
        //         'no_of_trades' => 4932,
        //         'deliv_qty' => 104198,
        //         'deliv_per' => 37.64
        //     ],
        //     [
        //         'symbol' => '21STCENMGM',
        //         'series' => 'BE',
        //         'date1' => '2024-08-16',
        //         'prev_close' => 97.11,
        //         'open_price' => 97.11,
        //         'high_price' => 99.05,
        //         'low_price' => 97.11,
        //         'last_price' => 99.05,
        //         'close_price' => 97.87,
        //         'avg_price' => 99.05,
        //         'ttl_trd_qnty' => 6108,
        //         'turnover_lacs' => 5.98,
        //         'no_of_trades' => 55,
        //         'deliv_qty' => 580282,
        //         'deliv_per' => 46.77
        //     ],
        //     [
        //         'symbol' => '360ONE',
        //         'series' => 'EQ',
        //         'date1' => '2024-08-16',
        //         'prev_close' => 1026.80,
        //         'open_price' => 1027.00,
        //         'high_price' => 1089.00,
        //         'low_price' => 1027.00,
        //         'last_price' => 1070.00,
        //         'close_price' => 1069.60,
        //         'avg_price' => 1068.72,
        //         'ttl_trd_qnty' => 1240680,
        //         'turnover_lacs' => 13259.42,
        //         'no_of_trades' => 58676,
        //         'deliv_qty' => 580282,
        //         'deliv_per' => 46.77
        //     ]
        // ];
        // foreach ($data as $record) {
        //     // Check if the symbol exists or create it
        //     $symbol = Symbol::firstOrCreate(['symbol' => $record['symbol']]);
        
        //     // Add symbol_id to the record
        //     $record['symbol_id'] = $symbol->id;

        //     // Insert or update the bhavcopies record
        //     Bhavcopy::updateOrCreate(
        //         ['symbol_id' => $record['symbol_id'], 'date1' => $record['date1']],
        //         $record
        //     );
        // }
        

        // return 'Data stored successfully';
    }
}