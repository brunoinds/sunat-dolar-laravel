<?php

namespace Brunoinds\SunatDolarLaravel\Converter;

use Illuminate\Support\Facades\Cache;
use DateTime;
use Carbon\Carbon;
use Brunoinds\SunatDolarLaravel\Store\Store;


class Converter{
    public static Store|null $store = null;

    public static function getDailyDollarExchangeRate(DateTime $date){
        $response = self::fetchMAPI($date);
        return $response;
    }
    public static function getDailySolesExchangeRate(DateTime $date){
        $response = self::fetchMAPI($date);
        $response->price->buy = (1 / $response->price->buy);
        $response->price->sell = (1 / $response->price->sell);
        return $response;
    }
    public static function convertDollarToSoles(DateTime $date, float $amount){
        $response = self::fetchMAPI($date);
        $price = $response->price->sell;
        $result = $amount * $price;
        return $result;
    }
    public static function convertSolesToDollar(DateTime $date, float $amount){
        $response = self::fetchMAPI($date);
        $price = $response->price->buy;
        $result = $amount / $price;
        return $result;
    }
    private static function fetchAPI(DateTime $date){
        if ($date->format('Y-m-d') > Carbon::now()->timezone('America/Lima')->format('Y-m-d')){
            $date = Carbon::now()->timezone('America/Lima')->toDateTime();
        }


        $dateString = $date->format('Y-m-d');

        $cachedValue = Converter::$store->get();

        if ($cachedValue){
            $stores = json_decode($cachedValue, true);
            if (isset($stores[$dateString])){
                return json_decode($stores[$dateString]);
            }
        }


        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha=' . $dateString,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Referer: https://apis.net.pe/tipo-de-cambio-sunat-api',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $results = json_decode($response);


        if (json_last_error() !== JSON_ERROR_NONE) {
            if (str_contains($response, 'Not Found')){
                return self::fetchAPI(Carbon::now()->timezone('America/Lima')->toDateTime());
            }
            throw new \Exception('Invalid JSON response: "' . json_last_error_msg(). '". The API response was: ' . $response);
        }

        if (!isset($results->compra)){
            return self::fetchAPI(Carbon::now()->timezone('America/Lima')->toDateTime());
        }

        $results = [
            'price' => [
                'buy' => $results->compra,
                'sell' => $results->venta
            ],
            'date' => DateTime::createFromFormat('Y-m-d', $results->fecha),
            'origin' => $results->origen
        ];
        $results = json_decode(json_encode($results));

        $stores[$dateString] = json_encode($results);
        Converter::$store->set(json_encode($stores));
        
        return $results;
    }
    private static function fetchMAPI(DateTime $date){
        if ($date->format('Y-m-d') > Carbon::now()->timezone('America/Lima')->format('Y-m-d')){
            $date = Carbon::now()->timezone('America/Lima')->toDateTime();
        }


        $dateString = $date->format('Y-m-d');
        $monthString = $date->format('m');
        $yearString = $date->format('Y');

        $cachedValue = Converter::$store->get();

        if ($cachedValue){
            $stores = json_decode($cachedValue, true);
            if (isset($stores[$dateString])){
                return json_decode($stores[$dateString]);
            }
        }


        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.apis.net.pe/v1/tipo-cambio-sunat?year='.$yearString.'&month=' . $monthString,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Referer: https://apis.net.pe/tipo-de-cambio-sunat-api',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $results = json_decode($response, true);


        if (json_last_error() !== JSON_ERROR_NONE) {
            if (str_contains($response, 'Not Found')){
                return self::fetchAPI(Carbon::now()->timezone('America/Lima')->toDateTime());
            }
            throw new \Exception('Invalid JSON response: "' . json_last_error_msg(). '". The API response was: ' . $response);
        }



        $resultsParsed = [];
        $result = null;
        foreach ($results as $value) {
            $resultParsed = [
                'price' => [
                    'buy' => $value['compra'],
                    'sell' => $value['venta']
                ],
                'date' => DateTime::createFromFormat('Y-m-d', $value['fecha']),
                'origin' => $value['origen']
            ];

            if ($value['fecha'] == $dateString){
                $result = $resultParsed;
            }

            $resultsParsed[$value['fecha']] = $resultParsed;
        }

        if ($result === null){
            throw new \Exception('Could not find the date in the API response: ' . $response);
        }


        foreach ($resultsParsed as $date => $result) {
            $stores[$date] = json_encode($result);
        }

        Converter::$store->set(json_encode($stores));
        

        $result = json_decode(json_encode($result));
        return $result;
    }
}