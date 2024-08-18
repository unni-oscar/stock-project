<?php

namespace App\Services;
use League\Csv\Exception;

use App\Services\CurlService;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\Symbol;
use App\Models\Bhavcopy;
use League\Csv\Reader;
use League\Csv\Writer;
use App\Traits\DateHelperTrait;

use App\Helpers\DataSanitizer;


class BhavcopyService
{
    protected $client;
    protected $curlService;
    use DateHelperTrait;

    public function __construct(CurlService $curlService)
    {
        $this->client = new Client();
        $this->curlService = $curlService;
    }

    public function fetchNseData($dateString = null)
    {
        // return  config('stock.url');
        // echo  $dateString;


        if ($dateString === null) {
            $date = Carbon::now();
        } else {
            $date = $dateString;
        }
        // else {

        //     // Determine if the date string is in '16-Aug-2024' or '16082024' format
        //     if (preg_match('/^\d{2}-[A-Za-z]{3}-\d{4}$/', $dateString)) {

        //         // If it's in '16-Aug-2024' format, parse it
        //         $date = Carbon::createFromFormat('d-M-Y', $dateString);

        //     } elseif (preg_match('/^\d{8}$/', $dateString)) {

        //         // If it's in '16082024' format, parse it
        //         $date = Carbon::createFromFormat('dmY', $dateString);

        //     } else {

        //         throw new \Exception(__('messages.invalid_date_with_sample'));

        //     }
    
        //     // Check if the parsed date is valid
        //     if (!$date || !$date->isValid()) {
        //         throw new \Exception(__('messages.invalid_date'));
        //     }
        // }
        // Format the date as 'ddMMyyyy' (e.g., '16082024')
        $dateInFileName = $this->convertToDate($date, 'dmY');
        // $formattedDate =  $date->format('dmY');

        // Generate the URL
        $nseUrl = config('stock.nse_url');
        $nseFileName = config('stock.nse_file_name');
        $nseFileExt = config('stock.nse_file_ext');
        $url = $nseUrl . $nseFileName . $dateInFileName . $nseFileExt ;
      
        $nseBhavcopy = $this->curlService->getRequest($url);
    
        if (!$nseBhavcopy ) {

            return [
                'success' => false,
                'message' => __('messages.nse_bhavcopy_error_message'),             
            ]; 

        } else {

            $bhavcopyFilePath = config('stock.bhavcopy_file_path');
            $directory = storage_path($bhavcopyFilePath);
            $filePath = $directory . $nseFileName . $dateInFileName . $nseFileExt;

            // Create directory if it does not exist
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true); 
            }

            file_put_contents($filePath, $nseBhavcopy);

            // $this->processCsvHeaders($filePath);

        }
        return [
            'success' => true,
            'message' => __('messages.nse_bhavcopy_success_message'),  
            'data' => [
                'date' => $dateInFileName
            ]           
        ];        

    }


    /**
     * Process the CSV file to remove spaces from headers and save it.
     *
     * @param string $filePath
     * @return void
     */
    protected function processCsvHeaders($filePath)
    {
    // Temporary file path for normalized CSV
    $tempFilePath = $filePath . '.tmp';

    try {
        // Debugging: Check file size before processing
        $originalFileSize = filesize($filePath);
        echo "Original File Size: " . $originalFileSize . " bytes\n";

        // Read the original CSV file
        $csvReader = Reader::createFromPath($filePath, 'r');
        $csvReader->setHeaderOffset(0);

        // Fetch headers and normalize by trimming spaces
        $headers = $csvReader->getHeader();
        $normalizedHeaders = array_map('trim', $headers);

        // Debugging: Check original and normalized headers
        echo "Original Headers:\n";
        print_r($headers);

        echo "Normalized Headers:\n";
        print_r($normalizedHeaders);

        // Read all records (rows) from the original CSV
        $records = iterator_to_array($csvReader->getRecords());

        // Debugging: Check records before processing
        echo "Records Before Processing:\n";
        foreach ($records as $record) {
            print_r($record);
        }

        // Create a new CSV Writer instance for the temporary file
        $csvWriter = Writer::createFromPath($tempFilePath, 'w+');

        // Write the normalized headers to the new CSV
        $csvWriter->insertOne($normalizedHeaders);

        // Write all records to the new CSV
        foreach ($records as $record) {
            // Map record values based on index positions
            $normalizedRecord = [];
            foreach ($headers as $index => $header) {
                $header = trim($header); // Ensure header is trimmed
                $normalizedRecord[] = $record[$header] ?? ''; // Use header name to index
            }
            $csvWriter->insertOne($normalizedRecord);
        }

        // Debugging: Check file size after processing
        $tempFileSize = filesize($tempFilePath);
        echo "Temporary File Size: " . $tempFileSize . " bytes\n";

        // Compare file sizes
        if ($originalFileSize === $tempFileSize) {
            echo "File size matches. Processing complete.\n";
        } else {
            echo "Warning: File size does not match. Original file size: $originalFileSize bytes, Temporary file size: $tempFileSize bytes.\n";
        }

        // Replace the original file with the new normalized file
        rename($tempFilePath, $filePath);

        // Confirm the original file has been replaced
        echo "CSV file processed and replaced successfully.\n";
    } catch (Exception $e) {
        echo "Error processing CSV file: " . $e->getMessage() . "\n";
        if (file_exists($tempFilePath)) {
            unlink($tempFilePath); // Clean up temp file if an error occurs
        }
    }
    }

    private function normalizeHeaders(array $headers): array
    {
        $normalizedHeaders = [];
        foreach ($headers as $header) {
            $normalizedHeaders[trim($header)] = trim($header);
        }
        return $normalizedHeaders;
    }
    public function processNseBhavcopy($response)
    {
        $bhavcopyFilePath = config('stock.bhavcopy_file_path');
        $nseFileName = config('stock.nse_file_name');
        $nseFileExt = config('stock.nse_file_ext');
        $path =  storage_path($bhavcopyFilePath.$nseFileName.$response['data']['date'].$nseFileExt);
       
        // $path = storage_path('app/bhavcopies/bhavcopy.csv');
        

        // Load the CSV file
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);             
     
        $records = $csv->getRecords();       

        foreach ($records as $record) {
            $symbol = $record['SYMBOL'] ?? '';
           // Attempt to parse date
        //    dd($record);
           $dateStr = DataSanitizer::sanitizeDate($record[' DATE1']) ?? '01-Jan-1970';
        //    echo $dateStr;die;
       // Attempt to parse date
       try {
        // Check if the date format is correct
        $date = $this->convertToDate($dateStr);
// echo $date;die;
    } catch (\Exception $e) {
        // Handle parsing error
        echo "Date parsing error5555555555: " . $e->getMessage() . "\n";
        // Fallback to default date if parsing fails
        $date = Carbon::createFromFormat('d-M-Y', '01-Jan-1970');
    }
// echo "<pre>";
//  print_r( DataSanitizer::sanitizeUnsignedBigInteger($record[' TTL_TRD_QNTY']) );
            // $date = Carbon::createFromFormat('d-M-Y', $record[' DATE1'] ?? '01-Jan-1970')->format('dmy'); // Default date if missing
            // print_r($date);
            $symbolRecord = Symbol::firstOrCreate(['symbol' => $symbol]);
            $bhavcopyExists = Bhavcopy::where('symbol_id', $symbolRecord->id)
                ->where('date1', $date)
                ->exists();

            if (!$bhavcopyExists) {
                
                Bhavcopy::create([
                    'symbol_id' => $symbolRecord->id,
                    'series' => DataSanitizer::sanitizeString($record[' SERIES'] ?? ''),
                    'date1' => $date,
                    'prev_close' => DataSanitizer::sanitizeDecimal($record[' PREV_CLOSE'] ?? 0),
                    'open_price' => DataSanitizer::sanitizeDecimal($record[' OPEN_PRICE'] ?? 0),
                    'high_price' => DataSanitizer::sanitizeDecimal($record[' HIGH_PRICE'] ?? 0),
                    'low_price' => DataSanitizer::sanitizeDecimal($record[' LOW_PRICE'] ?? 0),
                    'last_price' => DataSanitizer::sanitizeDecimal($record[' LAST_PRICE'] ?? 0),
                    'close_price' => DataSanitizer::sanitizeDecimal($record[' CLOSE_PRICE'] ?? 0),
                    'avg_price' => DataSanitizer::sanitizeDecimal($record[' AVG_PRICE'] ?? 0),
                    'ttl_trd_qnty' => DataSanitizer::sanitizeUnsignedBigInteger($record[' TTL_TRD_QNTY'] ?? 0),
                    'turnover_lacs' => DataSanitizer::sanitizeDecimal($record[' TURNOVER_LACS'] ?? 0),
                    'no_of_trades' => DataSanitizer::sanitizeUnsignedBigInteger($record[' NO_OF_TRADES'] ?? 0),
                    'deliv_qty' => DataSanitizer::sanitizeUnsignedBigInteger($record[' DELIV_QTY'] ?? 0),
                    'deliv_per' => DataSanitizer::sanitizeDecimal($record[' DELIV_PER'] ?? 0),
                ]);
            }
        }
        return [
            'success' => true,
            'message' => __('messages.nse_bhavcopy_insert_success_message'),             
        ]; 
    }


    public function fetchBSEData()
    {
        $url = 'URL_TO_BSE_BHAVCOPY'; // Replace with actual BSE URL
        $response = $this->client->get($url);
        return $response->getBody()->getContents();
    }
}
