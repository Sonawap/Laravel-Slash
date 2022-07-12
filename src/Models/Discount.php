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
    protected $table = 'coupons';

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

}
