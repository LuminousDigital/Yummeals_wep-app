<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Get referral leaderboard data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::withoutGlobalScopes()
            ->selectRaw('users.*, (SELECT COUNT(*) FROM users AS r WHERE r.referred_by = users.id AND r.deleted_at IS NULL) AS referrals_count')
            ->orderBy(
                $request->get('sort_by', 'referrals_count'),
                $request->get('sort_order', 'desc')
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $leaderboard = $query->paginate($perPage);

        return response()->json($leaderboard);
    }

    /**
     * Get top 3 referrers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topThree()
    {
        $topThree = User::withoutGlobalScopes()
            ->selectRaw('users.*, (SELECT COUNT(*) FROM users AS r WHERE r.referred_by = users.id AND r.deleted_at IS NULL) AS referrals_count')
            ->having('referrals_count', '>', 0)
            ->orderBy('referrals_count', 'desc')
            ->limit(3)
            ->get();

        return response()->json($topThree);
    }

    /**
     * Get user's referral details
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function userReferrals($userId)
    {
        $user = User::findOrFail($userId);

        $referrals = User::where('referred_by', $user->referral_code)
            ->select(['id', 'name', 'email', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'user' => $user,
            'referrals' => $referrals,
            'total_referrals' => $referrals->count()
        ]);
    }

    /**
     * Get leaderboard statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $stats = [
            'total_users' => User::withoutGlobalScopes()->count(),
            'total_referrals' => User::withoutGlobalScopes()->whereNotNull('referred_by')->count(),
            'top_referrer' => User::withoutGlobalScopes()
                ->selectRaw('users.*, (SELECT COUNT(*) FROM users AS r WHERE r.referred_by = users.id AND r.deleted_at IS NULL) AS referrals_count')
                ->having('referrals_count', '>', 0)
                ->orderBy('referrals_count', 'desc')
                ->first(),
            'average_referrals' => User::withoutGlobalScopes()
                ->selectRaw('users.*, (SELECT COUNT(*) FROM users AS r WHERE r.referred_by = users.id AND r.deleted_at IS NULL) AS referrals_count')
                ->having('referrals_count', '>', 0)
                ->avg('referrals_count') ?? 0
        ];

        return response()->json($stats);
    }
}
