<?php

declare(strict_types=1);

namespace App\Integration;

use App\Models\System\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Propaganistas\LaravelPhone\PhoneNumber;


class EskizService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('SMS_GATE_URL'),
            'headers' => [
                'Authorization' => 'Bearer ' . Setting::findSmsGateToken()->value,
            ],
            'timeout' => 10,
        ]);
    }

    public function login(): array
    {
        return $this->send('/api/auth/login', [
            [
                'name' => 'email',
                'contents' => env('SMS_GATE_EMAIL'),
            ],
            [
                'name' => 'password',
                'contents' => env('SMS_GATE_PASSWORD'),
            ],
        ]);
    }

    public function sendMessage(string $phoneNumber, string $message): array
    {
        $url = '/api/message/sms/send';
        $phoneNumber = PhoneNumber::make($phoneNumber);

        if ($phoneNumber->getCountry() !== 'UZ') {
            $url .= '-global';
        }

        return $this->send($url, [
            [
                'name' => 'mobile_phone',
                'contents' => (int)$phoneNumber->getRawNumber(),
            ],
            [
                'name' => 'country_code',
                'contents' => $phoneNumber->getCountry()
            ],
            [
                'name' => 'message',
                'contents' => $message,
            ]
        ]);
    }

    private function send(string $uri, array $formData = []): array
    {
        if (App::isProduction()) {
            $response = $this->client->post($uri, [
                'multipart' => $formData,
            ]);

            return json_decode(
                $response->getBody()->getContents(),
                true
            );
        }

        return [];
    }
}
