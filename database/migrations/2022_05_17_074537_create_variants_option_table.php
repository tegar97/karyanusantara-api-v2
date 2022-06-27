<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariantsOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variants_option', function (Blueprint $table) {
            $table->id();
            $table->integer('product_variantion_id');
            $table->string('variantName');
            $table->string('variantionImg');
            $table->string('sku');
            $table->bigInteger('price');
            $table->integer('product_stock_id');
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
        Schema::dropIfExists('variants_option');
    }
}
