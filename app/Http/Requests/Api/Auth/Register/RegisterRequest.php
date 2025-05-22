<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth\Register;

use App\Enum\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string $first_name
 * @property-read string $last_name
 * @property-read string $code
 * @property-read string $phone_number
 */
class RegisterRequest extends FormRequest
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
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
//            'code' => ['required', 'string'],
            'phone_number' => [
                'required',
                'phone:AUTO',
                Rule::unique('users', 'phone_number')
                    ->whereNot('status', UserStatus::DELETED->value)
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
