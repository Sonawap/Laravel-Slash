<?php

namespace Sonawap\Slash\Models;

use Sonawap\Slash\Models\Discount;
use Sonawap\Slash\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;

    protected $table = 'slash_coupons';

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public static function calculatePercentage($percent, $actualNumber){
        $count = ($percent / 100) * $actualNumber;
        $amount = $actualNumber - $count;
        return $amount;
    }

    public function discount() {
        return $this->belongsTo(Discount::class);
    }

    public static function checkIfCouponExistsByCode($code){
        $code = Coupon::where('code', $code)->first();
        if($code){
            return $code;
        }else{
            return null;
        }
    }

    public static function checkIfCouponExistsById($id){
        $code = Coupon::where('id', $id)->first();
        if($code){
            return $code;
        }else{
            return null;
        }
    }

    public static function createdCoupon(Discount $discount, $code){

        try {
            $coupon = new Coupon();
            $coupon->discount_id = $discount->id;
            $coupon->code = $code;
            $coupon->save();

            return $coupon;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function updateCoupon(Discount $discount,$code, Coupon $coupon){

        try {
            $coupon->discount_id = $discount->id;
            $coupon->code = $code;
            $coupon->save();

            return $coupon;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
