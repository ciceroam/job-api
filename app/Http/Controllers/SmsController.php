<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    private $Client;

    public function __construct()
    {
        $this->Client = new Client([
            'base_uri' => 'https://sms77io.p.rapidapi.com/sms',
            'timeout' => 60000,
        ]);
    }

    /**
     * Send SMS to a cellphone.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {


        return response()->json($request);

        $requestBody = [
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
                'x-rapidapi-host' => 'sms77io.p.rapidapi.com',
                'x-rapidapi-key' => '33baceefa6msha19e74ec26817c7p1f96f4jsn2a33520e0a4e'
            ],
            'form_params' => [
                'to' => '+5551992765331',
                'p' => 'KL6tTfiw5ZqazwJgp55ToiNHX8gJcHI6qeU1z6PrPDA3jPzMaZ7CyHSg7Y2egQ6c',
                'text' => 'Cicero Augusto é Foda \o/'
            ]
        ];

        $response = $this->Client->request(
            'POST',
            "",
            $requestBody
        )->getBody()
            ->getContents();

        return response()->json($response);
    }
}
