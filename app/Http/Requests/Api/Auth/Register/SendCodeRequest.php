<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth\Register;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string $phone_number
 */
class SendCodeRequest extends FormRequest
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
            'phone_number' => [
                'required',
                'phone:AUTO',
                Rule::unique('users', 'phone_number')
                    ->whereNull('deleted_at'),
            ],
        ];
    }
}
