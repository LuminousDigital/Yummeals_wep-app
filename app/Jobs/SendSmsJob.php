<?php

namespace App\Jobs;

use App\Services\SmsManagerService;
use App\Services\SmsService;
use App\Sms\VerifyCode;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $smsData;
    protected SmsManagerService $smsManagerService;
    protected string $gateway;

    public function __construct(array $smsData)
    {
        $this->smsData = $smsData;
    }

    public function handle(SmsManagerService $smsManagerService, SmsService $smsService)
    {
        try {
            $this->smsManagerService = $smsManagerService;
            $this->gateway = $smsService->gateway();

            if ($this->smsManagerService->gateway($this->gateway)->status()) {
                $verifyCode = new VerifyCode($this->smsData['token']);
                $this->smsManagerService->gateway($this->gateway)->send(
                    $this->smsData['code'],
                    $this->smsData['phone'],
                    $verifyCode->build()
                );
            }
        } catch (Exception $e) {
            Log::error('SMS sending failed', [
                'error' => $e->getMessage(),
                'data' => $this->smsData,
                'gateway' => $this->gateway ?? 'not_set'
            ]);

            $this->fail($e); // Mark job as failed
        }
    }
}
