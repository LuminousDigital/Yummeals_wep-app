<?php

namespace App\Services;


use Exception;
use GuzzleHttp\Client;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirebaseService
{
    public $filePath;

    public function sendNotification($data, $fcmTokens, $topicName): void 
    {
        try {
            $notification = Settings::group('notification')->all();
            $projectId = $notification['notification_fcm_project_id'] ?? null;
            $url = 'https://fcm.googleapis.com/v1/projects/' . $projectId . '/messages:send';

            Log::info('[FCM] Preparing to send', [
                'project_id' => $projectId,
                'topic'      => $topicName,
                'token_count'=> is_array($fcmTokens) ? count($fcmTokens) : 0,
                'title'      => $data->title ?? null,
            ]);

            $accessToken = $this->getAccessToken();
            $client  = new Client();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ];

            foreach ($fcmTokens as $fcmToken) {
                try {
                    $payload = [
                        'message' => [
                            'token' => $fcmToken,
                            'notification' => [
                                'title' => $data->title,
                                'body' => $data->description,
                                'image' => $data->image ?? null,
                            ],
                            'data' => [
                                'title' => $data->title,
                                'body' => $data->description,
                                'sound' => 'default',
                                'image' => $data->image ?? null,
                                'topicName' => $topicName,
                            ],
                            'webpush' => [
                                'headers' => [
                                    'Urgency' => 'high'
                                ]
                            ],
                        ],
                    ];

                    Log::info('[FCM] Sending to token', [
                        'token_masked' => $this->maskToken($fcmToken),
                        'title'        => $data->title ?? null,
                    ]);

                    $result = $client->post($url, [
                        'headers' => $headers,
                        'body'    => json_encode($payload)
                    ]);

                    $status = $result->getStatusCode();
                    $body   = (string) $result->getBody();
                    Log::info('[FCM] Sent', [
                        'token_masked' => $this->maskToken($fcmToken),
                        'status'       => $status,
                        'response'     => $body,
                    ]);
                } catch (\Throwable $ex) {
                    Log::error('[FCM] Send failed', [
                        'token_masked' => $this->maskToken($fcmToken),
                        'error'        => $ex->getMessage(),
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error('[FCM] Setup failed', ['error' => $e->getMessage()]);
        }
    }

    private function maskToken(?string $token): string
    {
        if (!$token) return '';
        $len = strlen($token);
        if ($len <= 10) return substr($token, 0, 3) . '…';
        return substr($token, 0, 6) . '…' . substr($token, -4);
    }

    function getAccessToken()
    {
        $keyFilePath = NotificationSetting::where(['key' => 'notification_fcm_json_file'])->first()->file;
        $parsed_url = parse_url($keyFilePath);

        if (isset($parsed_url['path'])) {
            $relative_path = ltrim($parsed_url['path'], '/storage');
            $this->filePath = storage_path('app/public/' . $relative_path);
        } else {
            Log::error('[FCM] No file found in the URL for service account');
            throw new Exception('No file found in the URL');
        }

        Log::info('[FCM] Using service account file', ['path' => $this->filePath]);
        $SCOPES = ['https://www.googleapis.com/auth/cloud-platform'];

        if (!file_exists($this->filePath)) {
            Log::error('[FCM] Service account key file not found', ['path' => $this->filePath]);
            throw new Exception('Service account key file not found');
        }

        $credentials = new ServiceAccountCredentials($SCOPES, $this->filePath);
        $token = $credentials->fetchAuthToken();

        if (isset($token['access_token'])) {
            Log::info('[FCM] Access token fetched successfully');
            return $token['access_token'];
        } else {
            Log::error('[FCM] Failed to fetch access token');
            throw new Exception('Failed to fetch access token');
        }
    }
}
