<?php

namespace Sonawap\Slash\Models;

use Sonawap\Slash\Http\Traits\UuidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    /*
        Set table
    */
    protected $table = 'slash_discounts';

    /**
     * Set the coupon discount relationship.
     *
     * @return void
    */
    public function coupons(){
        return $this->hasMany(Coupon::class);
    }

    /**
     * Check if the discount has expired
     *
     * @return boolean
    */
    public function checkIfDiscountDateIsValid(){
        $parsedFrom = Carbon::parse($this->start_date);
        $parsedTo = Carbon::parse($this->end_date);

        if(now()->between($parsedFrom, $parsedTo)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Check if the discount limit has not reach max
     *
     * @return boolean
    */

    public function checkIfDiscountHasNotExceedMax(Coupon $coupon){
        if($coupon){
            $couponUsage = CouponUsage::where('coupon_id', $coupon->id)->count();
            if($couponUsage > $this->max_usage){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Check if a model has not reach it limit for discount usage
     *
     * @return boolean
    */
    public function checkIfDiscountHasNotExceedMaxForModel(Coupon $coupon, $model_id, $model_usage){
        if($coupon){
            $couponUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('model_id', $model_id)
                ->where('model_usage', $model_usage)
                ->count();
            if($couponUsage > $this->max_usage_per_model){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public static function checkIfDiscountExistsById($discount_id){
        $discount = Discount::where('id', $discount_id)->first();
        if($discount){
            return $discount;
        }else{
            return null;
        }
    }

    public static function checkIfDiscountExistsByName($name){
        $discount = Discount::where('name', $name)->first();
        if($discount){
            return $discount;
        }else{
            return null;
        }
    }

    public static function createDiscount(
        $name,
        $assigned_to,
        $scope,
        $offer_type,
        $off_value,
        $max_usage,
        $max_usage_per_model,
        $start_date,
        $end_date
    ){
        try {
            $discount = new Discount();
            $discount->name = $name;
            $discount->assigned_to = $assigned_to;
            $discount->scope = $scope;
            $discount->offer_type = $offer_type;
            $discount->off_value = $off_value;
            $discount->max_usage = $max_usage;
            $discount->max_usage_per_model = $max_usage_per_model;
            $discount->start_date = $start_date ?? now();
            $discount->end_date = $end_date ?? now()->addMonths(3);

            $discount->save();

            return $discount;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function updateDiscount(
        $name,
        $assigned_to,
        $scope,
        $offer_type,
        $off_value,
        $max_usage,
        $max_usage_per_model,
        $start_date,
        $end_date,
        Discount $discount
    ){
        try {
            $discount->name = $name ?? $discount->name;
            $discount->assigned_to = $assigned_to ?? $discount->assign_to;
            $discount->scope = $scope ?? $discount->scope;
            $discount->offer_type = $offer_type ?? $discount->offer_type;
            $discount->off_value = $off_value ?? $discount->off_value;
            $discount->max_usage = $max_usage ?? $discount->max_usage;
            $discount->max_usage_per_model = $max_usage_per_model ?? $discount->max_usage_per_model;
            $discount->start_date = $start_date ?? $discount->start_date;
            $discount->end_date = $end_date ?? $discount->end_date;

            $discount->save();

            return $discount;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

}
