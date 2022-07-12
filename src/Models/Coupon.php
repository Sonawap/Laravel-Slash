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

    protected $table = 'slash_discounts';

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function discounts() {

        return $this->belongsTo(Discount::class);

    }
}
