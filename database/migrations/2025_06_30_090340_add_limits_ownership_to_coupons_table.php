<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('daily_limit_per_user')->nullable()->after('limit_per_user');
            $table->boolean('is_reusable')->default(false)->after('daily_limit_per_user');
            $table->boolean('is_winning_coupon')->default(false)->after('is_reusable');
            $table->foreignId('user_id')->nullable()->after('is_winning_coupon')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('daily_limit_per_user');
            $table->dropColumn('is_reusable');
            $table->dropColumn('is_winning_coupon');

            // Drop foreign key first before dropping column
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
