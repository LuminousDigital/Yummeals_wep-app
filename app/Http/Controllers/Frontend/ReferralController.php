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

        $topThree = $this->getLeaderboardQuery()
            ->take(3)
            ->get();

        $relativeLeaderboard = $this->getRelativeLeaderboard($user);

        return response()->json([
            'user' => [
                'name' => $user->name,
            ],
            'referral_code' => $user->referral_code,
            'total_referrals' => $user->total_referrals,
            'referral_balance' => $user->referral_balance,
            'referrals' => $user->referrals()
                ->select(['id', 'name', 'email', 'created_at'])
                ->latest()
                ->paginate(10),
            'leaderboard' => [
                'top_three' => $topThree,
                'relative_leaderboard' => $relativeLeaderboard,
            ]
        ]);
    }

    private function getRelativeLeaderboard(User $user)
    {
        $allUsers = $this->getLeaderboardQuery()->get();

        $userRank = null;
        $relativeUsers = collect();

        if ($allUsers->isNotEmpty()) {
            $userRank = $allUsers->search(function ($item) use ($user) {
                return $item->id === $user->id;
            });

            if ($userRank !== false) {
                $userRank += 1;

                $startIndex = max(0, $userRank - 3); 
                $endIndex = min($allUsers->count() - 1, $userRank + 1); 

                $relativeUsers = $allUsers->slice($startIndex, $endIndex - $startIndex + 1)
                    ->map(function ($user, $index) use ($startIndex) {
                        return [
                            'rank' => $startIndex + $index + 1,
                            'name' => $user->name,
                            'referral_code' => $user->referral_code,
                            'total_referrals' => $user->total_referrals,
                            'referral_balance' => $user->referral_balance,
                            'is_current_user' => $user->id === auth()->id(),
                        ];
                    });
            }
        }

        return [
            'user_rank' => $userRank,
            'total_users' => $allUsers->count(),
            'users' => $relativeUsers,
        ];
    }

    private function getLeaderboardQuery()
    {
        return User::query()
            ->select(['id', 'name', 'referral_code', 'total_referrals', 'referral_balance'])
            ->where('id', '!=', 1)
            ->where('total_referrals', '>', 0)
            ->orderBy('total_referrals', 'desc')
            ->orderBy('created_at', 'asc');
    }

    public function leaderboard()
    {
        $leaderboard = $this->getLeaderboardQuery()
            ->take(20)
            ->get()
            ->map(function ($user, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $user->id,
                    'name' => $user->name,
                    'referral_code' => $user->referral_code,
                    'total_referrals' => $user->total_referrals,
                    'referral_balance' => $user->referral_balance,
                ];
            });

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
            'amount' => 'required|numeric|min:10|max:' . $user->referral_balance
        ]);

        DB::transaction(function () use ($user, $request) {

            $user->decrement('referral_balance', $request->amount);

            $user->wallet()->increment('balance', $request->amount);

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
            'referral_code' => 'required|alpha_num|min:6|max:12|unique:users,referral_code,' . $user->id
        ]);

        $user->update([
            'referral_code' => Str::upper($request->referral_code)
        ]);

        return response()->json([
            'message' => 'Referral code updated successfully'
        ]);
    }
}