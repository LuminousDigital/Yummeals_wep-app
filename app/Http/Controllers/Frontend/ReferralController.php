<?php

namespace App\Http\Controllers\Frontend;

use App\Models\ReferralBonus;
use App\Models\ReferralTransaction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'referral_code' => $user->referral_code,
            'total_referrals' => $user->total_referrals,
            'referral_balance' => $user->referral_balance,
            'referrals' => $user->referrals()
                ->select(['id', 'name', 'email', 'created_at'])
                ->latest()
                ->paginate(10),
        ]);
    }

    public function leaderboard()
    {
        $leaderboard = User::query()
            ->select(['id', 'name', 'referral_code', 'total_referrals', 'referral_balance'])
            ->where('total_referrals', '>', 0)
            ->orderBy('total_referrals', 'desc')
            ->take(20)
            ->get();
            
        return response()->json([
            'leaderboard' => $leaderboard,
            'updated_at' => now()->toDateTimeString()
        ]);
    }

    public function bonuses(Request $request)
    {
        $user = $request->user();
        
        $bonuses = $user->givenBonuses()
            ->with(['referee:id,name,email'])
            ->latest()
            ->paginate(10);
            
        $stats = [
            'total_earned' => $user->givenBonuses()->sum('amount'),
            'available_balance' => $user->referral_balance,
            'pending_bonuses' => $user->givenBonuses()->where('status', 'pending')->sum('amount'),
            'paid_bonuses' => $user->givenBonuses()->where('status', 'completed')->sum('amount'),
        ];
        
        return response()->json([
            'stats' => $stats,
            'bonuses' => $bonuses
        ]);
    }

    public function claimBonus(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'amount' => 'required|numeric|min:10|max:'.$user->referral_balance
        ]);
        
        DB::transaction(function () use ($user, $request) {
            // Deduct from referral balance
            $user->decrement('referral_balance', $request->amount);
            
            // Add to wallet
            $user->wallet()->increment('balance', $request->amount);
            
            // Record ReferralTransaction
            ReferralTransaction::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'type' => 'referral_withdrawal',
                'status' => 'completed',
                'meta' => ['description' => 'Referral bonus withdrawal']
            ]);
        });
        
        return response()->json([
            'message' => 'Bonus claimed successfully',
            'new_balance' => $user->referral_balance
        ]);
    }

    public function updateReferralCode(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'referral_code' => 'required|alpha_num|min:6|max:12|unique:users,referral_code,'.$user->id
        ]);
        
        $user->update([
            'referral_code' => Str::upper($request->referral_code)
        ]);
        
        return response()->json([
            'message' => 'Referral code updated successfully'
        ]);
    }
}