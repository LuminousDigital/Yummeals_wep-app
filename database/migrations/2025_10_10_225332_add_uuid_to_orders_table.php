<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('uuid', 36)->nullable()->unique();
        });

        // Populate existing orders with UUID
        $orders = DB::table('orders')->whereNull('uuid')->get();
        foreach ($orders as $order) {
            DB::table('orders')->where('id', $order->id)->update(['uuid' => Str::uuid()]);
        }

        // Make uuid not nullable
        Schema::table('orders', function (Blueprint $table) {
            $table->string('uuid', 36)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
