# monitoring-metrics

Monitoring Metrics is a Component library designed to help you monitor and track various user activities on your website or application. This library enables you to easily capture and log metrics such as the number of clicks, reports, page views, and downloads. 

## Features

- **Click Tracking:** Monitor the number of times elements are clicked.
- **Page View Tracking:** Track the number of views each page receives.
- **Download Tracking:** Log and count file downloads.

## Installation

Install via Composer:

```bash
composer require glw/monitoring-metrics
```

## Usage

First, ensure that you have included the namespace and imported the necessary classes:

```php
use Glw\MonitoringMetrics\MonitoringMetrics;
```

## Tracking Clicks
To track a click event on a specific element, use the trackClick method:

```php
$metrics = new MonitoringMetrics();
$metrics->trackClick('button_123');
```

## Tracking Page Views
To track a page view, use the trackPageView method:

```php
$metrics->trackPageView('homepage');
```

## Tracking Downloads
To track a file download, use the trackDownload method:

```php
$metrics->trackDownload('/path/to/file.zip');
```


## Example
Below is an example of how you can use this library within a Laravel controller:

```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Glw\MonitoringMetrics\MonitoringMetrics;

class UserController extends Controller
{
    protected $metrics;

    public function __construct()
    {
        $this->metrics = new MonitoringMetrics();
    }

    public function trackUserClick(Request $request)
    {
        $elementId = $request->input('elementId');
        $this->metrics->trackClick($elementId);

        return response()->json(['status' => 'success']);
    }

    public function trackUserPageView(Request $request)
    {
        $page = $request->input('page');
        $this->metrics->trackPageView($page);

        return response()->json(['status' => 'success']);
    }

    public function trackUserDownload(Request $request)
    {
        $filePath = $request->input('filePath');
        $this->metrics->trackDownload($filePath);

        return response()->json(['status' => 'success']);
    }
}
```
