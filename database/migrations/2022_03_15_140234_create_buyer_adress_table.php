<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyerAdressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_address', function (Blueprint $table) {
            $table->id();
            $table->integer('buyers_id')->unsigned()->nullable();;
            $table->longText('complateAddress');
            $table->string('phoneNumber');
            $table->integer('postalCode');
            $table->boolean('isMainddress')->default(false);
            $table->string('labelAddress');
            $table->string('province_id');
            $table->string('province_name');
            $table->string('city_id');
            $table->string('city_name');
            $table->string('subdistrict_id');
            $table->string('subdistrict'); 
            $table->string('village');
            $table->string('courirMessage')->nullable();
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
        Schema::dropIfExists('buyer_adress');
    }
}
