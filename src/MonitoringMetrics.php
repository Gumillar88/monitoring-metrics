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
    protected $projectID;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
        
        $this->apiUrl = rtrim(getenv('API_METRICS_URL'), '/') . '/';
        $this->apiToken = getenv('API_TOKEN_METRICS');
        $this->projectID = getenv('PROJECT_ID');
    }

    public function trackClick()
    {
        $action = 'click';
        $method = 'GET';
        $project_id = $this->projectID;
        $function = 'trackClick()';

        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);
        
        $this->sendData($action, $get_traffic);
    }

    public function trackPageView($get_function)
    {
        $action = 'view';
        $method = 'GET';
        $project_id = $this->projectID;
        $function = $get_function;
        
        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);

        $this->sendData($action, $get_traffic);
    }

    public function trackDownload()
    {
        $action = 'download';
        $method = 'GET';
        $project_id = $this->projectID;
        $function = 'trackDownload()';

        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);

        $this->sendData($action, $get_traffic);
    }

    public function trackCreate()
    {
        $action = 'create';
        $method = 'GET';
        $project_id = $this->projectID;
        $function = 'trackCreate()';

        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);
        
        $this->sendData($action, $get_traffic);
    }

    public function trackSave()
    {
        $action = 'save';
        $method = 'POST';
        $project_id = $this->projectID;
        $function = 'trackSave()';

        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);
        
        $this->sendData($action, $get_traffic);
    }

    public function trackReports()
    {
        $action = 'reports';
        $method = 'GET';
        $project_id = $this->projectID;
        $function = 'trackReports()';

        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);
        
        $this->sendData($action, $get_traffic);
    }

    public function trackEdit()
    {
        $action = 'edit';
        $method = 'GET';
        $project_id = $this->projectID;
        $function = 'trackEdit()';

        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);

        $this->sendData($action, $get_traffic);
    }

    public function trackUpdate()
    {
        $action = 'update';
        $method = 'POST';
        $project_id = $this->projectID;
        $function = 'trackUpdate()';

        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);

        $this->sendData($action, $get_traffic);
    }

    public function trackDelete()
    {
        $action = 'delete';
        $method = 'POST';
        $project_id = $this->projectID;
        $function = 'trackDelete()';

        $get_traffic = $this->_getDataTraffic($action, $method, $project_id, $function);

        $this->sendData($action, $get_traffic);
    }

    protected function sendData($type, $value)
    {
        $this->_getBaseAPI($type, $value);
    }

    public function _getValueMetrics($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function _getDataTraffic($action, $method, $project_id, $function)
    {
        $data = [
            'timestamp'         => date('Y-m-d H:i:s'),
            'project_id'        => $project_id,
            'method'            => $method,
            'action'            => $action,
            'activity_url'      => url()->current(),
            'ip_address'        => $this->get_client_ip(),
            'app_host'          => $this->detectDevice(),
            'app_browser'       => $this->detectBrowser(),
            'function'          => $function,
            'headers'           => $this->_getHeaders(),
            'data_request'      => $this->_getRequest(),
            'data_response'     => $this->_getResponse(),
        ];

        return $data;
    }

    public function _getBaseAPI($type, $value)
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

        return $statusCode;
    }

    public function get_client_ip() {
        
        $ipaddress = '';
        
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public function detectDevice() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $deviceType = "Unknown Device";

        // Define regex patterns for different device types
        $devicePatterns = [
            'Mobile'    => '/mobile|android|kindle|silk|opera mini|opera mobi|blackberry|webos|windows phone|phone|pocket|palm|cricket|docomo|fone/i',
            'Tablet'    => '/tablet|ipad|playbook|silk/i',
            'Desktop'   => '/macintosh|windows|linux|x11/i',
            'Bot'       => '/bot|crawl|slurp|spider|mediapartners|adsbot|bingbot|googlebot|yandexbot/i',
        ];

        // Loop through patterns and match against the User-Agent
        foreach ($devicePatterns as $device => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $deviceType = $device;
                break;
            }
        }

        return $deviceType;
    }

    public function detectBrowser() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $browser = "Unknown Browser";

        // Define regex patterns for different browsers
        $browserPatterns = [
            'Edge'              => '/edge/i',
            'Internet Explorer' => '/msie|trident/i',
            'Opera'             => '/opera|opr/i',
            'Firefox'           => '/firefox/i',
            'Chrome'            => '/chrome|crios/i',
            'Safari'            => '/safari/i',
        ];

        // Loop through patterns and match against the User-Agent
        foreach ($browserPatterns as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $browser = $name;
                break;
            }
        }

        return $browser;
    }

    public function _getHeaders()
    {
        $headers = [
            'Authorization: Bearer '.$this->apiToken,
            'Content-Type: application/json',
        ];

        return json_encode($headers);
    }

    public function _getRequest()
    {
        $ch = curl_init($this->apiUrl.'track');

        $data = [
            'type' => 'tracking',
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


        $info = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        return json_encode($info);
    }

    public function _getResponse()
    {
        $ch = curl_init($this->apiUrl.'track');

        $data = [
            'type' => 'tracking',
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


        $info = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        return json_encode($info);
    }
}
