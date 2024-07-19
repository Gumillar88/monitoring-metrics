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
        $client = HttpClient::create();
        $url = $this->apiUrl . 'track';

        $postData = [
            'type' => $type,
            'value' => $value
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->apiToken,
            'Content-Type' => 'application/json'
        ];

        try {
            $response = $client->request('POST', $url, [
                'json' => $postData,
                'headers' => $headers,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getContent();

            error_log('Response status: ' . $statusCode);
            error_log('Response body: ' . $content);

            return $statusCode === 200;
        } catch (ExceptionInterface $e) {
            error_log('HttpClient error: ' . $e->getMessage());
            return false;
        }
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