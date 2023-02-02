<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class WeatherController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {

        $this->validate($request, [
            'city' => ['required']
        ]);

        $data['weather'] = $this->getPlaceWeather($request->city);
        $data['place'] = $this->getPlaceData($request->city);

        return response()->json($data, 200);
    }



    /**
     * @param string $city
     * @return json
     */
    public function getPlaceWeather(string $city) : array {

        try {
            $response = Http::get('http://api.openweathermap.org/data/2.5/forecast', [
                'appid' => env('OPENWEATHER_API_KEY'), // API KEY
                'q' => $city,  // city name,
                'cnt' => 5 // limited the count to 5
            ]);

        } catch (\Exception $ex) {
                throw $ex;
        }

        $result = json_decode($response->getBody());
        return $result->list;
    }

    /**
     * @param string $city
     * @return json
     */
    public function getPlaceData(string $city) : array {

        try {
            $response = Http::withHeaders([
                'Authorization' => env('FOURSQUARE_API_KEY'),
                'accept' => 'application/json',
            ])->get(
                'https://api.foursquare.com/v3/places/search',
                [
                    'near' => $city,
                ]
            );

        } catch (\Exception $ex) {
                throw $ex;
        }


        $result =  json_decode($response->getBody());
        return $result->results;
    }
}
