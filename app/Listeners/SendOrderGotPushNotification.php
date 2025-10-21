<?php

namespace App\Listeners;


use App\Events\SendOrderGotPush;
use App\Services\OrderGotPushNotificationBuilder;
use Illuminate\Support\Facades\Log;

class SendOrderGotPushNotification
{
    public function handle(SendOrderGotPush $event)
    {
        Log::info('[Listener] SendOrderGotPush received', $event->info ?? []);
        try{
            $orderPushNotificationBuilderService = new OrderGotPushNotificationBuilder($event->info['order_id']);
            $orderPushNotificationBuilderService->send();
        } catch(\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
