<?php

namespace App\Http\Requests\Api\Shop;

use App\Enum\CurrencyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string|null $tiktok_login
 * @property-read string|null $tiktok_password
 * @property-read string|null $pubg_id
 * @property-read CurrencyEnum|null $currency
 */
class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'tiktok_login' => ['sometimes', 'required', 'string'],
            'tiktok_password' => ['sometimes', 'required', 'string'],
            'pubg_id' => ['sometimes', 'required', 'string'],
            'currency' => ['sometimes', 'required', 'string', Rule::enum(CurrencyEnum::class)]
        ];
    }
}
