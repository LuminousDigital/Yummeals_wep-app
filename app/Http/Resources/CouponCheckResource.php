<?php

namespace App\Http\Resources;

use App\Enums\DiscountType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponCheckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $discountAmount = $this->is_winning_coupon
            ? $request->total
            : $this->amount($request);

        return [
            'id'                => $this->id,
            'code'              => $this->code,
            'is_winning_coupon' => $this->is_winning_coupon,
            'discount'          => $discountAmount,
            "flat_discount"     => AppLibrary::flatAmountFormat($discountAmount),
            "convert_discount"  => AppLibrary::convertAmountFormat($discountAmount),
            "currency_discount" => AppLibrary::currencyAmountFormat($discountAmount),
        ];
    }

    public function amount($request)
    {
        if ($this->discount_type == DiscountType::FIXED) {
            $amount = $this->discount;
            if ($amount > $this->maximum_discount) {
                return $this->maximum_discount;
        } else {
            $amount = ($request->total * ($this->discount) / 100);
            if ($amount > $this->maximum_discount) {
                return $this->maximum_discount;
            } else {
                return $amount;
            }
            }
            return $amount;
        } else {
            $amount = ($request->total * $this->discount / 100);
            if ($amount > $this->maximum_discount) {
                return $this->maximum_discount;
            }
            return $amount;
        }
    }
}
