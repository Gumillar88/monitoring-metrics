<?php

namespace Glw\MonitoringMetrics;

use GuzzleHttp\Client;
use Dotenv\Dotenv;

class MonitoringMetrics
{
    protected $client;
    protected $apiUrl;
    protected $apiToken;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
        
        $this->apiUrl = getenv('API_METRICS_URL');
        $this->apiToken = getenv('API_TOKEN');
        $this->client = new Client();
    }

    public function trackClick($elementId)
    {
        $this->sendData('click', $elementId);
    }

    public function trackPageView($page)
    {
        $this->sendData('page_view', $page);
    }

    public function trackDownload($filePath)
    {
        $this->sendData('download', $filePath);
    }

    protected function sendData($type, $value)
    {
        $response = $this->client->post($this->apiUrl . '/track', [
            'json' => [
                'type' => $type,
                'value' => $value,
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
            ]
        ]);

        return $response->getStatusCode() === 200;
    }

    public function _getValueMetrics($value)
    {
        // Fungsi helper untuk mendapatkan nilai metrics
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function _getBaseAPI($url)
    {
        $api_url = $url;
        $ch = curl_init();
        
        // Header Authorization
        $headers = [
            'Authorization: Bearer '.$this->apiToken.'',
        ];

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  // Menambahkan header Authorization

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            echo 'Error API: ' . curl_error($ch);
            $data = array();
        } else {
            $data = json_decode($response, true);
        }

        // Tutup sesi cURL
        curl_close($ch);

        return $data;
    }
}