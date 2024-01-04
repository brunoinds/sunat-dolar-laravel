<?php

namespace Brunoinds\SunatDolarLaravel\Converter;

use Illuminate\Support\Facades\Cache;
use DateTime;


class Converter{
    public static function getDailyDollarExchangeRate(DateTime $date){
        $response = self::fetchAPI($date);
        return $response;
    }
    public static function getDailySolesExchangeRate(DateTime $date){
        $response = self::fetchAPI($date);
        $response->price->buy = (1 / $response->price->buy);
        $response->price->sell = (1 / $response->price->sell);
        return $response;
    }
    public static function convertDollarToSoles(DateTime $date, float $amount){
        $response = self::getDailyDollarExchangeRate($date);
        $price = $response->price->sell;
        $result = $amount * $price;
        return $result;
    }
    public static function convertSolesToDollar(DateTime $date, float $amount){
        $response = self::getDailyDollarExchangeRate($date);
        $price = $response->price->buy;
        $result = $amount / $price;
        return $result;
    }
    private static function fetchAPI(DateTime $date){
        $dateString = $date->format('Y-m-d');

        $cachedValue = Cache::store('file')->get('Brunoinds/SunatDolarLaravelStore');

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
            throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
        }

        if (!isset($results->compra)){
            return self::fetchAPI(new DateTime());
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
        Cache::store('file')->put('Brunoinds/SunatDolarLaravelStore', json_encode($stores));
        
        return $results;
    }

}