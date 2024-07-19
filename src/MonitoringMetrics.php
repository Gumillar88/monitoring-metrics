<?php

namespace Glw\MonitoringMetrics;

use GuzzleHttp\Client;
use Dotenv\Dotenv;

class MonitoringMetrics
{
    protected $client;
    protected $apiUrl;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
        
        $this->apiUrl = getenv('API_METRICS_URL');
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
            ]
        ]);

        return $response->getStatusCode() === 200;
    }

    public function _getValueMetrics($value)
    {
        // Fungsi helper untuk mendapatkan nilai metrics
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}