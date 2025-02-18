<?php

namespace App\Http\Controllers;

use TelegramBot\Api\BotApi;

abstract class Controller
{
    public $bot;

    function __construct()
    {
        $this->bot = new BotApi('8105904526:AAETOmV9OIlreQ123GTzLvFlwrcRo80pTh0');
    }

    public function success(?string $message, $data = null, array $additional = [], $status = 200)
    {
        $response = [
            "status" => true,
            "message" => $message ?? "Your request has been granted",
        ];

        if (isset($data)) {
            $response["data"] = $data;
        }

        if (isset($additional)) {
            $response += $additional;
        }

        return response()->json($response, $status);
    }

    public function sentMessage(string $number, string $message)
    {
        try {
            $curl = curl_init();

            $pesan = [
                "messageType" => "text",
                "to" => $number,
                "body" => $message,
            ];

            $apiKey = env("STARSENDER_API_KEY");

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.starsender.online/api/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($pesan),
                CURLOPT_HTTPHEADER => [
                    'Content-Type:application/json',
                    "Authorization:$apiKey",
                ],
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        } catch (\Exception $e) {
            logger($e->getMessage());
            throw $e;
        }
    }

    public function paginate(?string $message, $data, $status = 200)
    {
        $response = [
            "status" => true,
            "message" => $message ?? "Your request has been granted",
        ];

        if (isset($data)) {
            $response += json_decode(json_encode($data), true);
        }

        return response()->json($response, $status);
    }

    public function invalid(?string $message, $data = null, $status = 400)
    {
        $response = [
            "status" => false,
            "message" => $message ?? "Your request has been failed",
        ];

        if (isset($data)) {
            $response["data"] = $data;
        }
        return response()->json($response, $status);
    }
}
