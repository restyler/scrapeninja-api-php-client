<?php

namespace ScrapeNinja;

use GuzzleHttp\Client as GuzzleClient;

class Client {
    private $guzzleClient;
    public function __construct(array $config = [])
    {
        if (!isset($config['rapidapi_key'])) {
            throw new \Exception('rapidapi_key must be set');
        }
        
        $this->guzzleClient = new GuzzleClient([
            // Base URI is used with relative requests
            'base_uri' => 'https://scrapeninja.p.rapidapi.com',
            // You can set any number of default request options.
            'timeout'  => 30,
            'headers' => [
                'X-RapidAPI-Host' => 'scrapeninja.p.rapidapi.com',
                'X-RapidAPI-Key' => $config['rapidapi_key'] // get your key on https://rapidapi.com/restyler/api/scrapeninja
            ]
        ]);

    }

    public function scrape(array $params = []) {
        if (!isset($params['url'])) {
            throw new \Exception('params[url] is required!');
        }
        
        $response = $this->guzzleClient->post('scrape', ['json' => $params]);

        return json_decode($response->getBody(), true);
    }


}