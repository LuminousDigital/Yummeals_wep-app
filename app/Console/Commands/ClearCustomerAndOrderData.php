<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MessageHistory;
use App\Models\User;
use App\Enums\Role;

class ClearCustomerAndOrderData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clear-customers-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely clears all order and customer-related tables without touching core system tables.';

    /**
     * Execute the console command.
     */
public function handle()
{
    $this->info('Clearing customer and order data...');

    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // 1) Clear all orders (children first)
    DB::table('order_items')->truncate();
    DB::table('order_addresses')->truncate();
    DB::table('order_coupons')->truncate();
    DB::table('transactions')->truncate();
    DB::table('capture_payment_notifications')->truncate();
    DB::table('orders')->truncate();

    // Re-enable foreign key checks after truncating order tables
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // 2) Find customer IDs
    $customerRoleId = \App\Enums\Role::CUSTOMER;
    if (!DB::table('roles')->where('id', $customerRoleId)->exists()) {
        $customerRoleId = DB::table('roles')->where('name', 'customer')->value('id');
    }

    $customerIds = DB::table('model_has_roles')
        ->where('model_type', \App\Models\User::class)
        ->where('role_id', $customerRoleId)
        ->pluck('model_id');

    // 3) Delete customer-related records
    if ($customerIds->isNotEmpty()) {
        $messageIds = DB::table('messages')->whereIn('user_id', $customerIds)->pluck('id');
        $mhIds = DB::table('message_histories')->whereIn('message_id', $messageIds)->pluck('id');
        DB::table('media')->where('model_type', \App\Models\MessageHistory::class)->whereIn('model_id', $mhIds)->delete();
        DB::table('message_histories')->whereIn('id', $mhIds)->delete();
        DB::table('messages')->whereIn('id', $messageIds)->delete();

        DB::table('personal_access_tokens')->where('tokenable_type', \App\Models\User::class)->whereIn('tokenable_id', $customerIds)->delete();
        DB::table('model_has_roles')->where('model_type', \App\Models\User::class)->whereIn('model_id', $customerIds)->delete();
        DB::table('addresses')->whereIn('user_id', $customerIds)->delete();
        DB::table('wallets')->whereIn('user_id', $customerIds)->delete();
        DB::table('referral_transactions')->whereIn('user_id', $customerIds)->delete();
        DB::table('referral_bonuses')->whereIn('referrer_id', $customerIds)->orWhereIn('referee_id', $customerIds)->delete();
        DB::table('notifications')->where('model_type', \App\Models\User::class)->whereIn('model_id', $customerIds)->delete();
        DB::table('push_notifications')->whereIn('user_id', $customerIds)->delete();
        DB::table('media')->where('model_type', \App\Models\User::class)->whereIn('model_id', $customerIds)->delete();

        \App\Models\User::whereIn('id', $customerIds)->forceDelete();
    }

    // 4) Optional ephemeral cleanup
    DB::table('otps')->truncate();

    $this->info('Customer and order data cleared successfully.');
}

}
