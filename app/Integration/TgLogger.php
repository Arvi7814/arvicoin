<?php

namespace App\Integration;

use GuzzleHttp\Client;
use Throwable;

class TgLogger
{
    private string $token;
    private Client $client;

    public function __construct()
    {
        $this->token = (string)env('TELEGRAM_BOT_TOKEN');
        $this->client = new Client([
            'base_uri' => 'https://api.telegram.org',
            'timeout' => 10,
        ]);
    }

    public function log(Throwable $e): void
    {
        $messages = [
            "<b>Code:</b> {$e->getCode()}",
            "<b>Message:</b> {$e->getMessage()}",
            "<b>Line:</b> {$e->getLine()}",
            "<b>Trance:</b>"
        ];

        foreach ($e->getTrace() as $index => $traceMessage) {
            if ($index > 4) break;

            try {
                $messages = array_merge($messages, [
                    "======================",
                    /** @phpstan-ignore-next-line */
                    "<b>File:</b> {$traceMessage['file']}",
                    /** @phpstan-ignore-next-line */
                    "<b>Line:</b> {$traceMessage['line']}",
                    /** @phpstan-ignore-next-line */
                    "<b>Class:</b> {$traceMessage['class']}"
                ]);
            } catch (Throwable $e) {

            }
        }

        try {
            $this->client->get("/bot$this->token/sendMessage", [
                'query' => [
                    'chat_id' => env('TELEGRAM_CHAT_ID'),
                    'text' => join(PHP_EOL, $messages),
                    'parse_mode' => 'HTML'
                ]
            ]);
        } catch (Throwable $e) {

        }
    }
}
