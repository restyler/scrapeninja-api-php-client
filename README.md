# ScrapeNinja Web scraper PHP API Client
This library is a thin Guzzle-based wrapper around ScrapeNinja web scraper API.


## What is ScrapeNinja?
Simple & high performance scraping API which emulates Chrome TLS fingerprint, backed by rotating proxies (geos: US, EU, Brazil, France, Germany, 4g residential proxies available!). Use this when node.js/curl/python fails to load the website even with headers fully identical to Chrome, but you still need fast scraping and want to avoid using Puppeteer and JS evaluation (ScrapeNinja returns RAW HTTP responses). 

### Read more about ScrapeNinja: 
https://pixeljets.com/blog/scrape-ninja-bypassing-cloudflare-403-code-1020-errors/ 





### Get your free access key here:
https://rapidapi.com/restyler/api/scrapeninja

See /examples folder for examples

# Installation
```
composer require restyler/scrapeninja-api-php-client
```

## Quick example:
### Basic scrape request
```php
use ScrapeNinja\Client;

$scraper = new Client([
        'rapidapi_key' => 'YOUR-RAPID-API_KEY'
    ]
);

$response = $client->scrape([
    'url' => 'https://news.ycombinator.com/',
    'geo' => 'us'
]);

print_r($response);
// response now contains JSON response, with .body property containing target website response.
/*
    {
        info: {
            version: '1.1',
            statusCode: 200,
            statusMessage: 'OK',
            headers: {
                server: 'nginx',
                'content-type': "text/html; charset=utf-8"
            }
        },
        body: {
            "<html lang="en" op="news"><head><meta name="referrer" content="origin"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" type="text/css" href="news.css?5eYyZbFhPFukXyt5EaSy">
        <link rel="shortcut icon" href="favic...."
        }
    }
    */
```
