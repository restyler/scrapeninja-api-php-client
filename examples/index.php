<?php
ini_set('display_errors', '1');
require __DIR__ . '/../vendor/autoload.php';

use ScrapeNinja\Client;

$client = new Client([
        'rapidapi_key' => 'YOUR-API-KEY' // get your key on https://rapidapi.com/restyler/api/scrapeninja
    ]
);


try {
    #########################
    ### basic GET scrape
    #########################
    $response = $client->scrape([
        'url' => 'https://news.ycombinator.com/'
    ]);
    
    echo '<h2>Basic scrape response:</h2><pre>';
    print_r($response);

    ####### RESPONSE STRUCTURE SAMPLE (JSON) ####
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

    echo '</pre>';



    echo '<h2>Basic scrape response:</h2><pre>';
    print_r($response);


    #########################
    ### sending POST request with EU proxy
    #########################
    $response = $client->scrape([
        'url' => 'https://news.ycombinator.com/',
        'method' => 'POST',
        'geo' => 'eu'
    ]);


    echo '<h2>POST scrape response:</h2><pre>';
    print_r($response);

    
    




    


    
} catch (GuzzleHttp\Exception\ClientException $e) {
    $response = $e->getResponse();
    
    echo 'Status code: ' . $response->getStatusCode() . "\n";
    echo 'Err message: ' . $e->getMessage() . "\n";
    

}



