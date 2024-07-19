<?php

namespace Glw\MonitoringMetrics;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
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
        
        $this->client = new Client();
        $this->apiUrl = rtrim(getenv('API_METRICS_URL'), '/') . '/';
        $this->apiToken = getenv('API_TOKEN_METRICS');
    }

    public function trackClick($elementId)
    {
        $this->sendData('click', $elementId);
    }

    public function trackPageView($page)
    {
        $this->sendData('view', $page);
    }

    public function trackDownload($filePath)
    {
        $this->sendData('download', $filePath);
    }

    protected function sendData($type, $value)
    {
        $url = $this->apiUrl . 'track';

        $postData = [
            'type' => $type,
            'value' => $value
        ];

        $headers = [
            'Authorization: Bearer ' . $this->apiToken,
            'Content-Type: application/json'
        ];
        $ch = curl_init($url);
        

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // dd($statusCode);
        if (curl_errno($ch)) {
            error_log('cURL error: ' . curl_error($ch));
            return false;
        }

        curl_close($ch);

        error_log('Response status: ' . $statusCode);
        error_log('Response body: ' . $response);

        return $statusCode === 200;
    }

    public function _getValueMetrics($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function _getBaseAPI($url)
    {
        $api_url = $url;
        $ch = curl_init();
        
        // Header Authorization
        $headers = [
            'Authorization: Bearer '.$this->apiToken,
        ];

        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  // Menambahkan header Authorization

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('Error API: ' . curl_error($ch));
            $data = [];
        } else {
            $data = json_decode($response, true);
        }

        // Tutup sesi cURL
        curl_close($ch);

        return $data;
    }
}