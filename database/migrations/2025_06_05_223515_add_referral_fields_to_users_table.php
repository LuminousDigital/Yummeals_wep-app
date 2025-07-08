<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->unique()->nullable();
            $table->unsignedBigInteger('referred_by')->nullable();
            $table->decimal('referral_balance', 10, 2)->default(0);
            $table->unsignedInteger('total_referrals')->default(0);
            
            $table->foreign('referred_by')->references('id')->on('users');
            $table->index('referral_code');
            $table->index('referred_by');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['referral_code', 'referred_by', 'referral_balance', 'total_referrals']);
        });
    }
};