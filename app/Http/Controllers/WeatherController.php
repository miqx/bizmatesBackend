<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WeatherController extends Controller
{

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
    public function getPlaceWeather(string $city) : object {

        try {
            $response = Http::get('http://api.openweathermap.org/data/2.5/forecast', [
                'appid' => env('OPENWEATHER_API_KEY'), // API KEY
                'q' => $city  // city name
            ]);

        } catch (\Exception $ex) {
                throw $ex;
        }


        return json_decode($response->getBody());
    }

    /**
     * @param string $city
     * @return json
     */
    public function getPlaceData(string $city) : object {

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


        return json_decode($response->getBody());
    }
}