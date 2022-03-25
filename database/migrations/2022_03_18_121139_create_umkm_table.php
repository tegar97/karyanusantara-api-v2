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
            $table->string('ukmName');
            $table->string('ownerName');
            $table->string('BussinessFormType');
            $table->date('businessStart');
            $table->string('ownerPhoneNumber');
            $table->string('ukmAddress');
            $table->integer('province_id');
            $table->string('province_name');
            $table->integer('city_id');
            $table->string('city_name');
            $table->integer('subdistrict_id');
            $table->string('subdistrict');
            $table->string('village');
            $table->integer('postalCode');
            $table->string('certificate');
            $table->string('certificateName');
            $table->integer('totalEmployee');
            $table->string('SocialMedia');
            $table->string('productSampleName');
            $table->string('productSampleCategory');
            $table->string('productSampleCapacity');
            $table->string('productSampleDescription');
            $table->string('productSamplePhoto');
            $table->string('annualIncome');
            $table->boolean('isAccept')->nullable();
            $table->string('rejectReason')->nullable();
            $table->integer('bankAccountNumber')->nullable();
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
