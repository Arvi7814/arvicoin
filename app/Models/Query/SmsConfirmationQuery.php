<?php

declare(strict_types=1);

namespace App\Models\Query;

use App\Models\System\SmsConfirmation;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<SmsConfirmation>
 */
class SmsConfirmationQuery extends Builder
{
    public function active(): self
    {
        return $this->where('expires_at', '>', now());
    }

    public function whereCode(string $code): self
    {
        return $this->where('code', $code);
    }

    public function whereType(string $type): self
    {
        return $this->where('type', $type);
    }

    public function wherePhoneNumber(string $phoneNumber): self
    {
        return $this->where('phone_number', $phoneNumber);
    }
}
