<?php

namespace App\Http\Requests\Api\User;

use App\Enum\LangEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string|null $first_name
 * @property-read string|null $last_name
 * @property-read string|null $language
 * @property-read string|null $latitude
 * @property-read string|null $longitude
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'required', 'string'],
            'last_name' => ['sometimes', 'required', 'string'],
            'language' => ['sometimes', 'required', Rule::enum(LangEnum::class)],
            'latitude' => ['sometimes', 'required', 'string'],
            'longitude' => ['sometimes', 'required', 'string']
        ];
    }
}
