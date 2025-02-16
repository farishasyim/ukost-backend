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
