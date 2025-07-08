<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('referral_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('type'); // e.g., 'deposit', 'withdrawal', 'referral_bonus'
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->json('meta')->nullable(); // Additional transaction data
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('referral_transactions');
    }
};