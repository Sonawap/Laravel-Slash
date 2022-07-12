<?php

namespace Sonawap\Slash\Http\Enums;

use App\Http\Controllers\Controller;

class ConditionType extends Controller{
    const DISCOUNT_ASSIGN_USER = 1;
    const DISCOUNT_ASSIGN_ADDRESS = 2;
    const DISCOUNT_SCOPE_GLOBAL = 1;
    const DISCOUNT_SCOPE_COUPON = 2;
    const DISCOUNT_SCOPE_PRODUCT = 3;
    const DISCOUNT_OFFER_TYPE_CURRENCY = 1;
    const DISCOUNT_OFFER_TYPE_PERCENT = 2;
    const DISCOUNT_STATUS_ACTIVE = 1;
    const DISCOUNT_STATUS_INACTIVE = 2;

}

