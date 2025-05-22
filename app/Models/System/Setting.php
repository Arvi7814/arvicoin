<?php

namespace App\Models\System;

use App\Enum\LangEnum;
use App\Enum\SettingsEnum;
use App\Helpers\LangHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property SettingsEnum $type
 * @property string $value
 * @property ?array $translations
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Setting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'translations' => 'json',
        'type' => SettingsEnum::class,
    ];

    /**
     * @return self
     */
    public static function findSmsGateToken(): Setting
    {
        return self::getSetting(SettingsEnum::SMS_GATE_TOKEN);
    }

    public static function tgInfo(): Setting
    {
        return self::getSetting(SettingsEnum::TG_INFO);
    }

    public static function tgOrderMessage(): Setting
    {
        return self::getSetting(SettingsEnum::TG_ORDER_TEXT);
    }

    public static function tgOrderClosedMessage(): Setting
    {
        return self::getSetting(SettingsEnum::TG_ORDER_CLOSED);
    }

    /**
     * @param SettingsEnum $enum
     * @return Setting
     */
    public static function getSetting(SettingsEnum $enum): Model
    {
        return self::query()->firstOrCreate([
            'type' => $enum
        ], ['value' => '', 'translations' => []]);
    }

    public function isTranslatable(): bool
    {
        return in_array($this->type, [
            SettingsEnum::ORDER_TEXT,
            SettingsEnum::TG_ORDER_TEXT,
            SettingsEnum::TG_INFO,
            SettingsEnum::TG_ORDER_CLOSED
        ]);
    }

    public function translation(): string
    {
        if (is_array($this->translations)) {
            return $this->translations[app()->getLocale()] ?? '';
        }

        return '';
    }
}
