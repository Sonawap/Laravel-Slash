<?php

namespace Sonawap\Slash;

use Sonawap\Slash\Models\Coupon;
use Sonawap\Slash\Models\Discount;
use Sonawap\Slash\Http\Enums\ConditionType;
use Sonawap\Slash\Models\CouponUsage;

class SlashCoupon
{
    public $model_usage;
    public $model_id;
    public $code;
    public $total_price;

    public function __construct($model_usage, $model_id, $code, $total_price){
        $this->model_usage = $model_usage;
        $this->code = $code;
        $this->model_id = $model_id;
        $this->total_price = $total_price;
    }
    /**
     * return all coupons
     *
     * @return array
    */
    public function index(){
        $coupons = Coupon::with('discounts')->latest()->get();
        return $coupons;
    }

    /**
     * return a coupon
     *
     * @return object
    */
    public function show($id){
        $coupon= Coupon::with('discounts')->findOrFail($id);
        return $coupon;
    }

    /**
     * check if code is validate
     *
     * @return string
    */
    public function getByCode($code){
        //check if code is validate
        $code = Coupon::where('code', $code)->first();

        if(!$code){
            return 'Coupon does not exists';
        }else{
            return $code;
        }
    }

    /**
     * use a coupon
     *
     * @return object
    */
    public function useCoupon(){

        //check if code is valid
        $code = Coupon::where('code', $this->code)->first();


        if(!$code){
            return 'Coupon is invalid';
        }

        //check if coupon has already been used for the product by the user
        $checkUsage = CouponUsage::where('model_usage', $this->model)
            ->where('user_id', $this->model_id)
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

        if($discount->checkIfDiscountHasNotExceedMaxForModel($code, $this->model_id, $this->model_usage)){
            return 'Sorry, model has exceed the maximum usage for this coupon';
        }


        try {
            $saveUsage = new CouponUsage();
            $saveUsage->model_usage = $this->model_usage;
            $saveUsage->model_id = $this->model_id;
            $saveUsage->coupon_id = $code->id;

            if($saveUsage->save()){
                if(strval($discount->offer_type) === '1'){
                    $price_after_coupon = $this->total_price - $discount->off_value;
                    if($price_after_coupon < 0){
                        return 0;
                    }
                    return $price_after_coupon;
                }
                else if(strval($discount->offer_type) == '2'){
                    $price_after_coupon = $this->calculatePercentage($discount->off_value, $this->total_price);
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

    public function calculatePercentage($percent, $actualNumber){
        $count = ($percent / 100) * $actualNumber;
        $amount = $actualNumber - $count;
        return $amount;
    }
}
