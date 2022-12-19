<?php
ini_set('display_errors', '1');
require __DIR__ . '/../vendor/autoload.php';

use ScrapeNinja\Client;

$client = new Client([
        'rapidapi_key' => getenv('SCRAPENINJA_RAPIDAPI_KEY') // get your key on https://rapidapi.com/restyler/api/scrapeninja
    ]
);


try {

    #########################
    ### using ScrapeNinja extractor function to extract data from HTML
    ### see https://scrapeninja.net/cheerio-sandbox/basic to get 
    ### more Cheerio syntax examples and to get REPL environment
    #########################
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

    


    
} catch (GuzzleHttp\Exception\ClientException $e) {
    $response = $e->getResponse();
    
    echo 'Status code: ' . $response->getStatusCode() . "\n";
    echo 'Err message: ' . $e->getMessage() . "\n";
    

}



