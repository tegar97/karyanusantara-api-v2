<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemcartsOngkir extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itemCarts', function (Blueprint $table) {
            $table->string('service_courier')->nullable();
            $table->string('courier_id')->nullable();
            $table->string('courier_price')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itemCarts', function (Blueprint $table) {
            //
        });
    }
}
