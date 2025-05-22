<?php
declare(strict_types=1);

namespace App\Enum;

interface UserState
{
    const INITIAL = 'initial';
    const CHOOSE_LANG = 'choose-lang';
    const ENTER_CONTACT = 'enter-contact';
    const NEUTRAL = 'neutral';
    const SHOW_SETTINGS = 'show-settings';
    const CHANGE_LANG = 'change-lang';
    const PRODUCT_SELECTED = 'product-selected';
    const ORDERED = 'ordered';
}
