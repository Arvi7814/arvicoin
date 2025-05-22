<?php

declare(strict_types=1);

namespace App\Http\Services;

class Tokenizer
{
    public function smsCode(): string
    {
        return (string)random_int(100000, 999999);
    }

    public function defaultCode(): string
    {
        return 'abcdef';
    }
}
