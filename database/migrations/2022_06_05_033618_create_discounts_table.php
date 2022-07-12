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
        Schema::create('slash_discounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->tinyInteger('scope')
                ->comment('1:coupon, 2:product')
                ->default(1)
                ->nullable();
            $table->tinyInteger('assigned_to')
                ->comment('1: user, 2:product', '3:location')
                ->default(1)
                ->nullable();
            $table->tinyInteger('offer_type')
                ->comment('1: $, 2: %')
                ->default(1)
                ->nullable();
            $table->integer('off_value')
                ->nullable();
            $table->integer('max_usage')
                ->nullable();
            $table->integer('max_usage_per_model')
                ->nullable();
            $table->tinyText('status')
                ->default(1)
                ->comment('1: active, 2: inactive')
                ->nullable();
            $table->timestamp('start_date')
                ->nullable();
            $table->timestamp('end_date')
                ->nullable();
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
        Schema::dropIfExists('slash_discounts');
    }
};
