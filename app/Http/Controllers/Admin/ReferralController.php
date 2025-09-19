<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function administratorReferrals($administratorId)
    {
        try {
            $administrator = User::withoutGlobalScopes()->find($administratorId);
            if (!$administrator) {
                return response()->json(['message' => 'Administrator not found'], 404);
            }
            $referrals = $administrator->referrals()->withoutGlobalScopes()->get();
            return response()->json(['data' => $referrals], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch referrals', 'error' => $e->getMessage()], 500);
        }
    }

    public function customerReferrals($customerId)
    {
        try {
            $customer = User::withoutGlobalScopes()->find($customerId);
            if (!$customer) {
                return response()->json(['message' => 'Customer not found'], 404);
            }
            $referrals = $customer->referrals()->withoutGlobalScopes()->get();
            return response()->json(['data' => $referrals], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch referrals', 'error' => $e->getMessage()], 500);
        }
    }
}
