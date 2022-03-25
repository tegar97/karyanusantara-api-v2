<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('category_id');
            $table->longText('description');
            $table->string('subcategory_id');
            $table->string('minimumOrder');
            $table->string('stock');
            $table->boolean('isReady')->nullable();
            $table->boolean('isPreorder')->default(false);
            $table->string('isPreOrderTime')->default('-');
            $table->string('price');
            $table->string('weight');
            $table->integer('umkm_id')->nullable();
            $table->integer('admin_id')->nullable();
            $table->integer('status')->nullable();

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
        Schema::dropIfExists('products');
    }
}
