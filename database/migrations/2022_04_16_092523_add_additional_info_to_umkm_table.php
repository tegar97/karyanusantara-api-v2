<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalInfoToUmkmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->boolean('StoreSettingDone')->default(0)->nullable();
            $table->boolean('GeneralInfomrationDone')->default(0)->nullable();
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('umkm', function (Blueprint $table) {
            //
        });
    }
}
