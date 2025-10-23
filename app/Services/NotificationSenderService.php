<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationSenderService
{
    public function __construct(private FirebaseService $firebase)
    {
    }

    public function sendToTokens(array $tokens, string $title, string $description, ?string $image = null, string $topic = 'general'): void
    {
        $tokens = array_values(array_filter($tokens));
        if (empty($tokens)) return;
        $payload = (object) [
            'title' => $title,
            'description' => $description,
            'image' => $image,
        ];
        try {
            $this->firebase->sendNotification($payload, $tokens, $topic);
        } catch (\Throwable $e) {
            Log::info('[NotificationSenderService] sendToTokens failed', ['error' => $e->getMessage()]);
        }
    }

    public function sendToUser(User $user, string $title, string $description, ?string $image = null, string $topic = 'general'): void
    {
        $tokens = [ $user->web_token ?? null, $user->device_token ?? null ];
        $this->sendToTokens($tokens, $title, $description, $image, $topic);
    }
}