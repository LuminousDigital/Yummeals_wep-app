<?php

namespace Database\Seeders;

use App\Enums\Activity;
use App\Models\GatewayOption;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewayTableSeederVersionThree extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $gateway = [
            'name'    => 'Cash Payment',
            'slug'    => 'cash',
            'misc'    => null,
            'status'  => Activity::ENABLE,
            'options' => []
        ];

        $payment = PaymentGateway::create([
            'name'   => $gateway['name'],
            'slug'   => $gateway['slug'],
            'misc'   => json_encode($gateway['misc']),
            'status' => $gateway['status'],
        ]);

        $imagePath = public_path('/images/payment-gateway/' . $gateway['slug'] . '.png');
        if (file_exists($imagePath)) {
            $payment->addMedia($imagePath)
                ->preservingOriginal()
                ->toMediaCollection('payment-gateway');
        }

        $this->gatewayOption($payment->id, $gateway['options']);
    }

    public function gatewayOption($id, $options): void
    {
        if (!blank($options)) {
            foreach ($options as $option) {
                GatewayOption::create([
                    'model_id'   => $id,
                    'model_type' => PaymentGateway::class,
                    'option'     => $option['option'],
                    'value'      => $option['value'] ?? '',
                    'type'       => $option['type'],
                    'activities' => json_encode($option['activities']),
                ]);
            }
        }
    }
}
