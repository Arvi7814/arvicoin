<?php
declare(strict_types=1);

namespace App\Integration;

use App\Models\System\Setting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Propaganistas\LaravelPhone\PhoneNumber;

final class PilotService
{
    private Client $client;
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = (string)env('PILOT_API_KEY');
        $this->client = new Client([
            'base_uri' => env('PILOT_GATE_URL'),
            'timeout' => 10,
        ]);
    }

    public function sendMessage(string $phoneNumber, string $message): void
    {
        $phoneNumber = PhoneNumber::make($phoneNumber);

        $this->send(
            $phoneNumber->getRawNumber(),
            $message,
        );
    }

    private function send(string $phoneNumber, string $message): void
    {
        if (App::isProduction()) {
            $this->client->get('api.php', [
                'query' => [
                    'send' => $message,
                    'to' => $phoneNumber,
                    'apikey' => $this->apiKey
                ]
            ]);
        }
    }
}
