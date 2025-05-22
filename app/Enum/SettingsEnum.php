<?php

declare(strict_types=1);

namespace App\Enum;

enum SettingsEnum: string
{
    case PHONE_NUMBER = 'phone-number';
    case ADDRESS = 'address';
    case ORDER_TEXT = 'order-text';
    case TG_ADDRESS = 'tg-address';
    case TG_ORDER_TEXT = 'tg-order-text';
    case TG_INFO = 'tg-info';
    case TG_ORDER_CLOSED = 'tg-order-closed';

    case SMS_GATE_TOKEN = 'sms-gate-token';

    /**
     * @return array<string, mixed>
     */
    public static function options(): array
    {
        return [
            self::PHONE_NUMBER->value => trans('fields.phone_number'),
            self::ADDRESS->value => trans('fields.address'),
            self::SMS_GATE_TOKEN->value => trans('fields.sms_gate_token'),
            self::ORDER_TEXT->value => trans('fields.order_text'),
            self::TG_ADDRESS->value => trans('fields.tg_address'),
            self::TG_ORDER_TEXT->value => trans('fields.tg_order_text'),
            self::TG_INFO->value => trans('fields.tg_info'),
            self::TG_ORDER_CLOSED->value => trans('fields.tg_order_closed'),
        ];
    }

    /**
     * @return SettingsEnum[]
     */
    public static function publicSettings(): array
    {
        return [
            self::PHONE_NUMBER,
            self::ADDRESS,
            self::ORDER_TEXT,
            self::TG_ADDRESS
        ];
    }
}
