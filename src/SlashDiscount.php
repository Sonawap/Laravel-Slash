<?php

namespace Sonawap\Slash;

use Sonawap\Slash\Http\Enums\ConditionType;
use Sonawap\Slash\Models\Discount;

class SlashDiscount
{
    public static function allDiscounts(){
        $discounts = Discount::latest()->get();
        return $discounts;
    }

    public static function getDiscountsByScope($scope){
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

    public static function showDiscount($id){
        $discount = Discount::findOrFail($id);
        return $discount;
    }

    public static function deleteDiscount($id){
        $discount = Discount::findOrFail($id);
        $discount->delete();
        return "Discount deleted";
    }

    public static function createDiscount(
        $name,
        $assigned_to = 2,
        $scope = 1,
        $off_value= 30,
        $offer_type= 2,
        $max_usage=10,
        $max_usage_per_mode = 10,
        $start_date = null,
        $end_date = null)
        {
            $discount = Discount::checkIfDiscountExistsByName($name);

            if($discount){
                return "Discount Name already exists";
            }

            try {
                return Discount::createDiscount(
                    $name,
                    $assigned_to,
                    $scope,
                    $offer_type,
                    $off_value,
                    $max_usage,
                    $max_usage_per_mode,
                    $start_date,
                    $end_date
                );
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
    }

    public static function updateDiscount(
        $name,
        $assign_to = null,
        $scope = null,
        $off_value=null,
        $offer_type= null,
        $max_usage=null,
        $max_usage_per_mode = null,
        $start_date = null,
        $end_date = null,
        $discount_id
        )
        {
            $discount = Discount::checkIfDiscountExistsById($discount_id);

            if(!$discount){
                return "Discount does not exists";
            }

            try {
                return Discount::updateDiscount(
                    $name,
                    $assign_to,
                    $scope,
                    $offer_type,
                    $off_value,
                    $max_usage,
                    $max_usage_per_mode,
                    $start_date,
                    $end_date,
                    $discount
                );
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
    }

}
