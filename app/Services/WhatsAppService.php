<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $url = "https://api.appsenders.com/api/create-message";

    protected $appKey;

    protected $authKey;

    public function __construct()
    {
        $this->appKey  = env('WHATSAPP_APP_KEY');
        $this->authKey = env('WHATSAPP_AUTH_KEY');
    }

    public function sendRequest(string $phoneCode, string $phoneNumber, string $message)
    {

        $data = [
            'appkey' => $this->appKey,
            'authkey' => $this->authKey,
            'to' => $phoneCode . $phoneNumber,
            'message' => $message,
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($this->url, $data);
        return $response->json();
    }
}
