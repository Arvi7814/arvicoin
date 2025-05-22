<?php

namespace App\Http\Requests\Api\Shop;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read string $product
 * @property-read string $count
 */
class SubProductRequest extends FormRequest
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
            'product' => ['required', Rule::exists('products', 'id')],
            'count' => ['required', 'integer', 'min:1']
        ];
    }
}
