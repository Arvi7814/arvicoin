<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Chat;

use App\Http\Services\Chat\Message;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

/**
 * @property-read string|null $message
 * @property-read UploadedFile|null $media
 */
class MessageRequest extends FormRequest
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
            'message' => ['required_without:media', 'nullable', 'string'],
            'media' => ['nullable', 'required_without:message', 'file']
        ];
    }

    public function toMessage(): Message
    {
        return new Message(
            $this->message,
            $this->media
        );
    }
}
