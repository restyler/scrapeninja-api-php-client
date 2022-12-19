# ScrapeNinja Web scraper PHP API Client
This library is a thin Guzzle-based wrapper around [ScrapeNinja Web Scraping API](https://scrapeninja.net/).


## What is ScrapeNinja?
Simple & high performance web scraping API which 
 - has 2 modes of websites rendering: 
    - `scrape()`: fast, which emulates Chrome TLS fingerprint without Puppeteer/Playwright overhead
    - `scrapeJs()`: full fledged real Chrome with Javascript rendering and basic interaction ([clicking, filling in forms](https://scrapeninja.net/scraper-sandbox?slug=interact-click)). 
 - is backed by rotating proxies (geos: US, EU, Brazil, France, Germany, 4g residential proxies available, your own proxy can be specified as well upon request). 
 - has smart retries and timeouts working out of the box
 - allows to extract arbitrary data from raw HTML without dealing with PHP HTML parsing libraries: just pass `extractor` function, written in JavaScript, and it will be executed on ScrapeNinja servers. ScrapeNinja uses Cheerio which is a jQuery-like library to extract data from HTML, you can quickly build & test your extractor function in [Live Cheerio Sandbox](https://scrapeninja.net/cheerio-sandbox/), see `/examples/extractor.php` for an extractor which gets pure data from HackerNews HTML source.

## ScrapeNinja Full API Documentation
https://rapidapi.com/restyler/api/scrapeninja

## ScrapeNinja Live Sandbox
ScrapeNinja allows you to quickly create and test your web scraper in browser: https://scrapeninja.net/scraper-sandbox


## Use cases
The popular use case of ScrapeNinja is when regular Guzzle/cURL fails to get the scraped website response reliably, even with headers fully identical to real browser, and gets 403 or 5xx errors instead.

Another major use case is when you want to avoid Puppeteer setup and maintenance but you still need real Javascript rendering instead of sending raw network requests.

ScrapeNinja helps to reduce the amount of code for retrieving HTTP responses and dealing with retries, proxy handling, and timeouts.

### Read more about ScrapeNinja: 
https://pixeljets.com/blog/bypass-cloudflare/ 
https://scrapeninja.net



### Get your free access key here:
https://rapidapi.com/restyler/api/scrapeninja

See /examples folder for examples

# Installation
```
composer require restyler/scrapeninja-api-php-client
```

# Examples:
`/examples` folder of this repo contains quick ready-to-launch examples how ScrapeNinja can be used.
To execute these examples in a terminal, [retrieve your API key](https://rapidapi.com/restyler/api/scrapeninja) and then set it as environment variable:
```bash
export SCRAPENINJA_RAPIDAPI_KEY=YOUR-KEY
php ./examples/extractor.php 
```

### Basic scrape request
```php
use ScrapeNinja\Client;

$scraper = new Client([
        "rapidapi_key" => getenv('SCRAPENINJA_RAPIDAPI_KEY')
    ]
);

$response = $client->scrape([
  // target website URL
  "url" => "https://news.ycombinator.com/", 
  
  // Proxy geo. eu, br, de, fr, 4g-eu, us proxy locations are available. Default: "us"
  "geo" => "us", 
  
  // Custom headers to pass to target website. Space after ':' is mandatory according to HTTP spec. 
  // User-agent header is not required, it is attached automatically.
  "headers" => ["Some-custom-header: header1-val", "Other-header: header2-val"], 
  
  "method" => "GET" // HTTP method to use. Default: "GET". Allowed: "GET", "POST", "PUT". 
]);

echo '<h2>Basic scrape response:</h2><pre>';

// response contains associative array with response, with 
// 'body'  containing target website response (as a string) and 
// 'info' property containing all the metadata.
echo 'HTTP Response status: ' . $response['info']['statusCode'] . "\n";
echo 'HTTP Response status: ' . print_r($response['info']['headers'], 1) . "\n";
echo 'HTTP Response body (truncated): ' . mb_substr($response['body'], 0, 300) . '...' . "\n";


/*
    Array
(
    [info] => Array
        (
            [version] => 1.1 (string)
            [statusCode] => 200 (integer)
            [statusMessage] => OK (string)
            [headers] => Array
                (
                    [server] => nginx
                    [date] => Mon, 02 May 2022 04:38:12 GMT
                    [content-type] => text/html; charset=utf-8
                    [content-encoding] => gzip
                )

        )

    [body] => <html lang="en" op="news"><head><meta name="referrer" content="origin"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" type="text/css" href="news.css?5eYyZbFhPFukXyt5EaSy">...
)
    */
```


# Get full HTML rendered by real browser (Puppeteer) in PHP:
```php
$response = $client->scrapeJs([
    "url" => "https://news.ycombinator.com/"
]);
```


# Extract data from raw HTML:
```php

// javascript extractor function, executed on ScrapeNinja servers 
$extractor = "// define function which accepts body and cheerio as args
    function extract(input, cheerio) {
        // return object with extracted values              
        let $ = cheerio.load(input);
      
        let items = [];
        $('.titleline').map(function() {
                  let infoTr = $(this).closest('tr').next();
                  let commentsLink = infoTr.find('a:contains(comments)');
                items.push([
                    $(this).text(),
                      $('a', this).attr('href'),
                      infoTr.find('.hnuser').text(),
                      parseInt(infoTr.find('.score').text()),
                      infoTr.find('.age').attr('title'),
                      parseInt(commentsLink.text()),
                      'https://news.ycombinator.com/' + commentsLink.attr('href'),
                      new Date()
                ]);
            });
      
      return { items };
    }";

$response = $client->scrapeJs([
    'url' => 'https://scrapeninja.net/samples/hackernews.html',
    'extractor' => $extractor
]);


echo '<h2>Extractor function test:</h2><pre>';
print_r($response['extractor']);
```

Response will contain PHP array with pure data:
```
(
    [result] => Array
        (
            [items] => Array
                (
                    [0] => Array
                        (
                            [0] => A bug fix in the 8086 microprocessor, revealed in the die's silicon (righto.com)
                            [1] => https://www.righto.com/2022/11/a-bug-fix-in-8086-microprocessor.html
                            [2] => _Microft
                            [3] => 216
                            [4] => 2022-11-26T22:28:40
                            [5] => 66
                            [6] => https://news.ycombinator.com/item?id=33757484
                            [7] => 2022-12-19T09:20:53.875Z
                        )

                    [1] => Array
                        (
                            [0] => Cache invalidation is one of the hardest problems in computer science (surfingcomplexity.blog)
                            [1] => https://surfingcomplexity.blog/2022/11/25/cache-invalidation-really-is-one-of-the-hardest-things-in-computer-science/
                            [2] => azhenley
                            [3] => 126
                            [4] => 2022-11-26T03:43:06
                            [5] => 66
                            [6] => https://news.ycombinator.com/item?id=33749677
                            [7] => 2022-12-19T09:20:53.878Z
                        )

                    [2] => Array
                        (
                            [0] => FCC Bans Authorizations for Devices That Pose National Security Threat (fcc.gov)
                            [1] => https://www.fcc.gov/document/fcc-bans-authorizations-devices-pose-national-security-threat
                            [2] => terramex
                            [3] => 236
                            [4] => 2022-11-26T20:01:49
                            [5] => 196
                            [6] => https://news.ycombinator.com/item?id=33756089
                            [7] => 2022-12-19T09:20:53.881Z
                        )
    ....
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
ScrapeNinja retries the request 2 times (so 3 requests in total) by default, in case of failure (target website timeout, proxy timeout, certain provider captcha request).
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

# Error handling
You should definitely wrap scrape() calls into try catch handler and log your errors. RapidAPI might get down, ScrapeNinja server might get down, target website might get down. 
- In case RapidAPI or ScrapeNinja are down, you will get Guzzle exception which treats any non-200 response from ScrapeNinja server as an unusual situation (which is good). You might get 429 error if you exceed your plan limit. 
- In case ScrapeNinja failed to get "good" response even after 3 retries it might throw 503 error.

In all these cases, it is useful to get HTTP response of a failure. 

```php
try {
   $response = $ninja->scrape($requestOpts);
   
   // you might want to add your custom errors here
   if ($response['info']['statusCode'] != 200) {
     throw new \Exception('your custom exception because this you didn\'t expect this from target website');
   }
} catch (GuzzleHttp\Exception\ClientException $e) {
    $response = $e->getResponse();
    
    echo 'Status code: ' . $response->getStatusCode() . "\n";
    echo 'Err message: ' . $e->getMessage() . "\n";
    

} catch (\Exception $e) {
   // your custom error handling logic, this is a non-Guzzle error
}
```

(see examples/ folder for full error handling example)
