<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth\Login;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $phone_number
 * @property-read string $code
 */
class LoginRequest extends FormRequest
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
            'phone_number' => ['required', 'phone:AUTO'],
//            'code' => ['required'],
        ];
    }
}
