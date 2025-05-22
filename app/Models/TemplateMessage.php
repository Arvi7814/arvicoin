<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $number
 * @property array $message
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TemplateMessage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'message' => 'json'
    ];
}
