<?php
namespace App\Library\Services;
use Illuminate\Support\Facades\DB;

class LowestRateService
{
    private $accessToken;
    private $client;

    public function main()
    {
        $this->client = new \GuzzleHttp\Client();
        if (session('accessToken')) {
            // echo "Access token is present in session <br>";
            $this->accessToken = session('accessToken');
            // echo $this->accessToken;

            try {
                $res = $this->client->get("https://test.api.amadeus.com/v1/security/oauth2/token/{$this->accessToken}");
                $valid = $this->getBody($res);

                if ($valid->state && $valid->state == 'expired') {
                    $this->getAccessToken();
                    session(['accessToken' => $this->accessToken]);
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                dd($e->getResponse()->getBody()->getContents());
            }
        } else {
            // echo "Access token not present in session, fetching the token <br>";
            $this->getAccessToken();
            // echo "Fetched the token putting in session: " . $this->accessToken;
            session(['accessToken' => $this->accessToken]);
            // echo "Stored access token in session";
        }

        $cities = DB::table('lowest_fairs')->get();


        for ($i = 0; $i < count($cities); $i++) {
            // print_r($cities[$i]->from_airport);
            // echo "<br>";

            try {
                $res = $this->client->get('https://test.api.amadeus.com/v1/shopping/flight-offers', [
                    'headers' =>
                    [
                        'Authorization' => "Bearer {$this->accessToken}"
                    ],
                    'query' => [
                        'origin' => $cities[$i]->from_airport,
                        'destination' => $cities[$i]->to_airport,
                        'departureDate' => date('Y-m-d'),
                        'max' => '1',
                    ]
                ]);
                // print_r(json_decode($res->getBody()->read(10000000)));
                $rate = $this->getBody($res);
                $lowest_price = $rate->data[0]->offerItems[0]->price->total;
                echo "<pre>";
                // print_r($rate);
                // print_r($rate->data[0]->offer_items[0]->price->total);
                echo $cities[$i]->from_airport . ' to ' . $cities[$i]->to_airport . ' lowest rate today is: ' . $lowest_price;
                echo "</pre>";
                DB::table('lowest_fairs')
                    ->where('id', $cities[$i]->id)
                    ->update(['lowest_fare' => $lowest_price]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                dd($e->getResponse()->getBody()->getContents());
            }
        }
    }

    function getAccessToken()
    {
        $res = $this->client->post('https://test.api.amadeus.com/v1/security/oauth2/token', [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => '4RzQx7SJiHAWnRqsENzeCx9zKwDJXR1Q',
                'client_secret' => 'OCrgOpvU4GOsLkfQ'
            ]
        ]);
        //  echo $res->getStatusCode(); // 200
        $data = $this->getBody($res);

        $this->accessToken = $data->access_token;
    }

    function getBody($response)
    {
        return json_decode($response->getBody()->read(1000000));
    }
}

