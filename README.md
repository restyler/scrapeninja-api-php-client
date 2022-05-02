# ScrapeNinja Web scraper PHP API Client
This library is a thin Guzzle-based wrapper around ScrapeNinja web scraper API.


## What is ScrapeNinja?
Simple & high performance scraping API which 
 - emulates Chrome TLS fingerprint, 
 - backed by rotating proxies (geos: US, EU, Brazil, France, Germany, 4g residential proxies available, your own proxy can be specified as well upon request). 
 - has smart retries and timeouts working out of the box

Use this when node.js/curl/python fails to load the website even with headers fully identical to Chrome, but you still need fast scraping and want to avoid using Puppeteer and JS evaluation (ScrapeNinja returns RAW HTTP responses). 
ScrapeNinja helps to dramatically reduce the amount of code for retrieving HTTP responses and dealing with retries, proxy handling, and timeouts.

### Read more about ScrapeNinja: 
https://pixeljets.com/blog/scrape-ninja-bypassing-cloudflare-403-code-1020-errors/ 





### Get your free access key here:
https://rapidapi.com/restyler/api/scrapeninja

See /examples folder for examples

# Installation
```
composer require restyler/scrapeninja-api-php-client
```

# Quick example:
### Basic scrape request
```php
use ScrapeNinja\Client;

$scraper = new Client([
        "rapidapi_key" => "YOUR-RAPID-API-KEY"
    ]
);

$response = $client->scrape([
    "url" => "https://news.ycombinator.com/", // target website URL
    "geo" => "us", // Proxy geo. eu, br, de, fr, 4g-eu, us proxy locations are available. Default: "us"
    "headers" => ["Some-custom-header: header1-val", "Other-header: header2-val"], // Custom headers to pass to target website. User-agent header is not required, it is attached automatically.
    "method" => "GET" // HTTP method to use. Default: "GET". Allowed: "GET", "POST", "PUT". 
]);

print_r($response);
// response now contains JSON response, with .body property containing target website response.
/*
    {
        info: {
            version: "1.1",
            statusCode: 200,
            statusMessage: "OK",
            headers: {
                server: "nginx",
                "content-type": "text/html; charset=utf-8"
            }
        },
        body: {
            "<html lang="en" op="news"><head><meta name="referrer" content="origin"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" type="text/css" href="news.css?5eYyZbFhPFukXyt5EaSy">
        <link rel="shortcut icon" href="favic...."
        }
    }
    */
```

# Sending POST requests

ScrapeNinja can perform POST requests.

## Sending JSON POST
```php
$response = $client->scrape([
    "url" => "https://news.ycombinator.com/", 
    "headers" => ["Content-Type: application/json"], 
    "method" => "POST" 
    "data" => "{\"fefe\":\"few\"}"
]);
```


## Sending www-encoded POST
```php
$response = $client->scrape([
    "url" => "https://news.ycombinator.com/", 
    "headers" => ["Content-Type: application/x-www-form-urlencoded"], 
    "method" => "POST" 
    "data" => "key1=val1&key2=val2"
]);
```



# Retries logic
ScrapeNinja retries the request 2 times (so 3 requests in total) by default, in case of failure (target website timeout, proxy timeout).
This behaviour can be modified and disabled.

ScrapeNinja can also be instructed to retry on  http response status codes and text existing in response body (useful for custom captchas)
```php
$response = $client->scrape([
    "url" => "https://news.ycombinator.com/",
    "retryNum": 1, // 0 to disable retries
    "textNotExpected": [
        "random-captcha-text-which-might-appear"
    ],
    "statusNotExpected": [
        403,
        502
    ]
]);
```
