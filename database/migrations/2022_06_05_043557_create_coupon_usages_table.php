<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slash_coupon_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model_usage');
            $table->string('model_id');
            $table->foreignUuid('coupon_id')
                ->references('id')
                ->on('slash_coupons')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slash_coupon_usages');
    }
};
