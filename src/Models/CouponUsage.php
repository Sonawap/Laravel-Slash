<?php

namespace Sonawap\Slash\Models;

use Sonawap\Slash\Http\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponUsage extends Model
{
    protected $table = 'slash_coupons_usage';
    use HasFactory;
    use UuidTrait;
    use SoftDeletes;
}
