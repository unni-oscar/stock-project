<?php

namespace App\Services;

use GuzzleHttp\Client;

class BhavcopyService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchNSEData()
    {
        $url = 'URL_TO_NSE_BHAVCOPY'; // Replace with actual NSE URL
        $response = $this->client->get($url);
        return $response->getBody()->getContents();
    }

    public function fetchBSEData()
    {
        $url = 'URL_TO_BSE_BHAVCOPY'; // Replace with actual BSE URL
        $response = $this->client->get($url);
        return $response->getBody()->getContents();
    }
}
