<?php

namespace Sonawap\Slash;

use Sonawap\Slash\Http\Enums\ConditionType;
use Sonawap\Slash\Models\Discount;

class SlashDiscount
{
    public function index(){
        $discounts = Discount::latest()->get();
        return $discounts;
    }

    public function getByScope($scope){
        $discounts = [];

        if($scope == "ALL"){
            $discounts = Discount::latest()->get();
        }

        else if($scope == "COUPON"){
            $discounts = Discount::where('scope', ConditionType::DISCOUNT_SCOPE_COUPON)
                ->get();
        }

        else if($scope == "PRODUCT"){
            $discounts = Discount::where('scope', ConditionType::DISCOUNT_SCOPE_PRODUCT)
                ->get();
        }

        else{
            $discounts = Discount::where('scope', ConditionType::DISCOUNT_SCOPE_GLOBAL)
                ->get();
        }

        return $discounts;
    }

    public function show($id){
        $discount = Discount::findOrFail($id);
        return $discount;
    }

}
