<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class WeatherService
{
    private $client;
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->client = HttpClient::create();
        $this->apiKey = $apiKey;
    }

    /**
     * @return array
     */
    public function getWeather($city)
    {
        try {
            $response = $this->client->request('GET',
            'https://api.openweathermap.org/data/2.5/weather?'
            . 'q='.$city
            . '&units=metric'
            . '&lang=fr'
            . '&appid=' . $this->apiKey);
            $content = $response->getContent();
            return json_decode($content, true);
        } catch (\Exception $e) {
            $statusCode = 0;
            if (method_exists($e, 'getResponse')) {
                $statusCode = $e->getResponse()->getStatusCode();
                echo $statusCode;
            }
            if ($statusCode == 0) {
                return 'Error occurs';
            }
            if(401 == $statusCode) {
                return "API calls return an error 401.
                You can get the error 401 in the following cases:

                You did not specify your API key in API request.
                Your API key is not activated yet. Within the next couple of hours, it will be activated and ready to use.
                You are using wrong API key in API request. Please, check your right API key in personal account.
                You have free subscription and try to get access to our paid services (for example, 16 days/daily forecast API, any historical weather data, Weather maps 2.0, etc). Please, check your tariff in your personal account and pay attention at our price and condition.
                Starting from 9 October 2015 our API requires a valid APPID for access. Note that this does not mean that our API is subscription-only now - please take a minute to register a free account to receive a key.

                For FOSS developers: we welcome free and open source software and are willing to help you. If you want to use OpenWeather data in your free software application please register an API key and file a ticket describing your application and API key registered. OpenWeather will review your request lift access limits for your key if used in open source application.";
            }
            if(404 == $statusCode) {
                return "API calls return an error 404.
                You can get this error in the following cases:

                You make a wrong API request. Please, check your API request. The detail documentation about any our weather services is here.
                You specify wrong city name, ZIP-code or city ID.

                This list contains the following data by locations in our system:

                City name
                City ID
                Geographical coordinates of the city (lon, lat)
                Zoom, etc";
            }
            if(429 == $statusCode) {
                return "API calls return an error 429.
                You will get the error 429 if you have free tariff and make more than 60 API calls per minute.

                Please switch to a subscription plan that meets your needs or reduce the number of API calls in accordance with the established limits.";
            }
        }
    }
}
