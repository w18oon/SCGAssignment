<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Restaurant extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // place api endpoint
        $api_base_path = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
        
        // google api key
        $api_key = 'AIzaSyCDPlulPVAaittDa8VVP_u_OVGQ_yOqa5s';
        
        // set default location for call place api
        $keyword = 'bangsue';

        // declare for get result from api
        $results = [];

        // assign keyword when user enter keyword
        if ($request->location) {
            $keyword = $request->location;
        }

        // check if exists in cache
        if (Cache::get($keyword)) {
            $results = Cache::get($keyword);
        } else {
            // create a client 
            $client = new \GuzzleHttp\Client();
            // send a request with query string
            $response = $client->request('GET', $api_base_path, [
                'query' => [
                    'key' => $api_key,
                    'query' => "restaurant-in-$keyword",
                ]
            ]);

            // check if a status code = 200
            if ($response->getStatusCode() == 200) {
                // get string body and decode to json format
                $body = $response->getBody();
                $contents = json_decode($body->getContents());

                // reformat result
                foreach ($contents->results as $result) {
                    $results[] = [
                        'name' => $result->name,
                        'addr' => $result->formatted_address,
                        'location' => [
                            'lat' => $result->geometry->location->lat,
                            'lng' => $result->geometry->location->lng,
                        ],
                    ];
                }

                // store result to cache
                Cache::put($keyword, $results, 600);
            }
        }

        // return response with json format
        return response()->json([
            'results' => $results
        ], 200);
    }
}
