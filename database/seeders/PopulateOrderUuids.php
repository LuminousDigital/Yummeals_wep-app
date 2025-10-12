<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PopulateOrderUuids extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = DB::table('orders')->whereNull('uuid')->get();

        foreach ($orders as $order) {
            DB::table('orders')->where('id', $order->id)->update(['uuid' => Str::uuid()]);
        }
    }
}
