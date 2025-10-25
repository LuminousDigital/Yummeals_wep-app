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
        // Safe-to-clear tables
        // ORDERS: order_items, order_addresses, order_coupons, transactions, capture_payment_notifications, orders
        // CUSTOMERS-RELATED: addresses, wallets, message_histories, messages, personal_access_tokens, referral_transactions, referral_bonuses, notifications (user rows), push_notifications (user rows), media (User, MessageHistory), model_has_roles, otps
        // DO NOT TRUNCATE: users (except customers), roles, permissions, settings, items, categories, taxes, branches, menus, languages, currencies

        // 1) Clear all orders (children first)
        DB::table('order_items')->truncate();
        DB::table('order_addresses')->truncate();
        DB::table('order_coupons')->truncate();
        DB::table('transactions')->truncate();
        DB::table('capture_payment_notifications')->truncate();
        DB::table('orders')->truncate();

        // 2) Find customer IDs
        $customerRoleId = Role::CUSTOMER;
        if (!DB::table('roles')->where('id', $customerRoleId)->exists()) {
            $customerRoleId = DB::table('roles')->where('name', 'customer')->value('id');
        }
        $customerIds = DB::table('model_has_roles')
            ->where('model_type', User
            ::class)
            ->where('role_id', $customerRoleId)
            ->pluck('model_id');

        // 3) Delete customer-related records
        if ($customerIds->isNotEmpty()) {
            // Messages and media attached to them
            $messageIds = DB::table('messages')->whereIn('user_id', $customerIds)->pluck('id');
            $mhIds = DB::table('message_histories')->whereIn('message_id', $messageIds)->pluck('id');
            DB::table('media')->where('model_type', MessageHistory::class)->whereIn('model_id', $mhIds)->delete();
            DB::table('message_histories')->whereIn('id', $mhIds)->delete();
            DB::table('messages')->whereIn('id', $messageIds)->delete();

            // Tokens, roles pivot
            DB::table('personal_access_tokens')->where('tokenable_type', User::class)->whereIn('tokenable_id', $customerIds)->delete();
            DB::table('model_has_roles')->where('model_type', User::class)->whereIn('model_id', $customerIds)->delete();

            // Other customer-linked tables
            DB::table('addresses')->whereIn('user_id', $customerIds)->delete();
            DB::table('wallets')->whereIn('user_id', $customerIds)->delete();
            DB::table('referral_transactions')->whereIn('user_id', $customerIds)->delete();
            DB::table('referral_bonuses')->whereIn('referrer_id', $customerIds)->orWhereIn('referee_id', $customerIds)->delete();
            DB::table('notifications')->where('model_type', User
            ::class)->whereIn('model_id', $customerIds)->delete();
            DB::table('push_notifications')->whereIn('user_id', $customerIds)->delete();

            // Media attached to users
            DB::table('media')->where('model_type', User::class)->whereIn('model_id', $customerIds)->delete();

            // Finally delete the customers themselves (hard delete; not soft)
            User::whereIn('id', $customerIds)->forceDelete();
        }

        // 4) Optional ephemeral cleanup
        DB::table('otps')->truncate();

        $this->info('Customer and order data cleared successfully.');
    }
}
