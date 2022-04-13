<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->integer('amount');
            $table->integer('shipping_amount');
            $table->string('logistic_code');
            $table->string('logistic_type');
            $table->integer('umkm_id');
            $table->integer('buyers_id');
            $table->integer('payment_id');
            $table->longText('buyers_complate_address');
            $table->string('resi')->nullable();
            $table->integer('status')->default(2);
            $table->string('status_str')->default('Diproses');;

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
        Schema::dropIfExists('transaction');
    }
}
