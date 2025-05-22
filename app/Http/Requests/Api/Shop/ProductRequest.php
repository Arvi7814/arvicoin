<?php

namespace App\Http\Requests\Api\Shop;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string|null $search
 * @property-read string|null $tag
 */
class ProductRequest extends FormRequest
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
        ];
    }
}
