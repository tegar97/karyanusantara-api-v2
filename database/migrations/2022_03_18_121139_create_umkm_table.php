<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUmkmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('umkm', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('password');
            $table->string('ukmName')->nullable();;
            $table->string('ownerName')->nullable();;
            $table->string('BussinessFormType')->nullable();;
            $table->date('businessStart')->nullable();;
            $table->string('ownerPhoneNumber')->nullable();;
            $table->string('ukmAddress')->nullable();;
            $table->integer('province_id')->nullable();;
            $table->string('province_name')->nullable();;
            $table->integer('city_id')->nullable();;
            $table->string('city_name')->nullable();;
            $table->integer('subdistrict_id')->nullable();;
            $table->string('subdistrict')->nullable();;
            $table->string('village')->nullable();;
            $table->integer('postalCode')->nullable();;
            $table->string('certificate')->nullable();;
            $table->string('certificateName')->nullable();;
            $table->integer('totalEmployee')->nullable();;
            $table->string('SocialMedia')->nullable();;
            $table->string('productSampleName')->nullable();;
            $table->string('productSampleCategory')->nullable();;
            $table->string('productSampleCapacity')->nullable();;
            $table->string('productSampleDescription')->nullable();;
            $table->string('productSamplePhoto')->nullable();;
            $table->string('annualIncome')->nullable();;
            $table->boolean('isAccept')->nullable();
            $table->string('rejectReason')->nullable();
            $table->bigInteger('bankAccountNumber')->nullable();
            $table->string('bankAccountName')->nullable();
            $table->string('bankAccountType')->nullable();
            $table->string('profile_photo')->nullable();
            $table->boolean('isInterestedToJoinUmkmid')->default(false);
            $table->boolean('isVerify')->default(false);
            $table->string('email_verify_expire')->nullable();
            $table->string('email_verify_token')->nullable();
            $table->string('password_reset_token')->nullable();
            $table->string('transaction_success_count')->nullable();
            $table->string('transaction_canceled_count')->nullable();
            $table->string('transaction_onProcess_count')->nullable();
            $table->double('rating')->nullable();
            $table->string('slug')->nullable();
            $table->string('description')->nullable();
            $table->string('npwp_no')->nullable();
            $table->string('npwp_photo')->nullable();
            $table->string('ktp_nik')->nullable();
            $table->string('ktp_photo')->nullable();
            $table->rememberToken();
            $table->date('password_reset_token_expire')->nullable();
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
        Schema::dropIfExists('umkm');
    }
}
