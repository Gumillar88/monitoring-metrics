<?php

namespace Glw\MonitoringMetrics;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

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
        $ch = curl_init($this->apiUrl.'track');

        $data = [
            'type' => 'view',
            'value' => $value,
        ];

        $headers = [
            'Authorization: Bearer '.$this->apiToken,
            'Content-Type: application/json',
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_VERBOSE, true);  // Enable verbose output
        curl_setopt($ch, CURLOPT_HEADER, true);   // Include headers in output

        $response = curl_exec($ch);
        
        if ($response === false) {
            error_log('cURL error: ' . curl_error($ch));
        } else {
            // Split headers and body
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);

            // Log headers and body separately
            error_log('Response Headers: ' . $header);
            error_log('Response Body: ' . $body);
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($statusCode != 200) {
            error_log('Error: HTTP status code ' . $statusCode);
        }

        curl_close($ch);
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