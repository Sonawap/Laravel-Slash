<?php

namespace Sonawap\Slash;

use Sonawap\Slash\Models\Coupon;
use Sonawap\Slash\Models\Discount;
use Sonawap\Slash\Http\Enums\ConditionType;
use Sonawap\Slash\Models\CouponUsage;

class SlashCoupon
{
    /**
     * return all coupons
     *
     * @return array
    */
    public static function allCoupons(){
        $coupons = Coupon::with('discount')->latest()->get();
        return $coupons;
    }

    /**
     * return a coupon
     *
     * @return object
    */
    public static function showCoupon($id){
        $coupon= Coupon::with('discount')->findOrFail($id);
        return $coupon;
    }

    /**
     * return a string
     *
     * @return string
    */
    public static function deleteCoupon($id){
        $coupon= Coupon::findOrFail($id);
        $coupon->delete();
        return "Coupon deleted successfully";
    }

    /**
     * check if code is validate
     *
     * @return string
    */
    public static function getCouponByCode($code){
        //check if code is validate
        $code = Coupon::with('discount')->where('code', $code)->first();

        if(!$code){
            return 'Coupon does not exists';
        }else{
            return $code;
        }
    }

    /**
     * create a coupon
     *
     * @return object
    */
    public static function createCoupon($discount_id, $code){
        $coupon = Coupon::checkIfCouponExistsByCode($code);
        if($coupon){
            return 'Coupon Code is aleady generated';
        }

        try {
            $discount = Discount::checkIfDiscountExistsById($discount_id);
            return Coupon::createdCoupon($discount, $code);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * update a coupon
     *
     * @return object
    */
    public static function updateCoupon($discount_id, $code, $coupon_id){
        $coupon = Coupon::checkIfCouponExistsById($coupon_id);

        if(!$coupon){
            return 'Coupon code does not exists';
        }

        try {
            $discount = Discount::checkIfDiscountExistsById($discount_id);
            return Coupon::updateCoupon($discount, $code, $coupon);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * use a coupon
     *
     * @return object
    */
    public static function useCoupon($model_usage, $model_id, $code, $total_price){

        //check if code is valid
        $code = Coupon::checkIfCouponExistsByCode($code);


        if(!$code){
            return 'Coupon is invalid';
        }

        //check if coupon has already been used for the product by the user
        $checkUsage = CouponUsage::where('model_usage', $model_usage)
            ->where('model_id', $model_id)
            ->where('coupon_id', $code->id)
            ->exists();
        if($checkUsage){
            return "Coupon has already been used by this model";
        }

        //check if coupon discount is valid (Checking date)
        $discount = Discount::where('id', $code->discount_id)->first();

        if(!$discount){
            return 'Coupon is invalid';
        }

        if(strval($discount->scope) !== '2'){
            return 'Coupon is invalid or Discount is not assigned to coupon';
        }

        if(!$discount->checkIfDiscountDateIsValid()){
            return 'Sorry, Coupon has expired';
        }

        if($discount->checkIfDiscountHasNotExceedMax($code)){
            return 'Sorry, Coupon has exceed maximum usage';
        }

        if($discount->checkIfDiscountHasNotExceedMaxForModel($code, $model_id, $model_usage)){
            return 'Sorry, model has exceed the maximum usage for this coupon';
        }


        try {
            $saveUsage = new CouponUsage();
            $saveUsage->model_usage = $model_usage;
            $saveUsage->model_id = $model_id;
            $saveUsage->coupon_id = $code->id;

            if($saveUsage->save()){
                if(strval($discount->offer_type) === '1'){
                    $price_after_coupon = $total_price - $discount->off_value;
                    if($price_after_coupon < 0){
                        return 0;
                    }
                    return $price_after_coupon;
                }
                else if(strval($discount->offer_type) == '2'){
                    $price_after_coupon = Coupon::calculatePercentage($discount->off_value, $total_price);
                    if($price_after_coupon < 0){
                        return 0;
                    }

                    return $price_after_coupon;
                }
            }else{
                return 'Sorry, you cannot use this coupon at this moment';
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

}
