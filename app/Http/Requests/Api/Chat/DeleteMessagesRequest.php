<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read int[] $messages
 */
class DeleteMessagesRequest extends FormRequest
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
            'messages' => ['required', 'array'],
            'messages.*' => ['required', Rule::exists('chat_messages', 'id')]
        ];
    }
}
