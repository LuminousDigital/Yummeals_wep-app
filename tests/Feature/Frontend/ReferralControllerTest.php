<?php

namespace Tests\Feature\Frontend;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class ReferralControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_returns_user_referral_data_with_leaderboard()
    {
        // Create test users with different referral counts
        $user1 = User::factory()->create([
            'total_referrals' => 10,
            'referral_balance' => 100,
            'created_at' => now()->subDays(5)
        ]);

        $user2 = User::factory()->create([
            'total_referrals' => 15,
            'referral_balance' => 150,
            'created_at' => now()->subDays(3)
        ]);

        $user3 = User::factory()->create([
            'total_referrals' => 20,
            'referral_balance' => 200,
            'created_at' => now()->subDays(1)
        ]);

        $currentUser = User::factory()->create([
            'total_referrals' => 12,
            'referral_balance' => 120,
            'created_at' => now()->subDays(2)
        ]);

        // Create some users with 0 referrals (should not appear in leaderboard)
        User::factory()->create(['total_referrals' => 0]);

        Log::info('Test Users Created:', [
            'user1' => ['id' => $user1->id, 'referrals' => $user1->total_referrals, 'created' => $user1->created_at],
            'user2' => ['id' => $user2->id, 'referrals' => $user2->total_referrals, 'created' => $user2->created_at],
            'user3' => ['id' => $user3->id, 'referrals' => $user3->total_referrals, 'created' => $user3->created_at],
            'currentUser' => ['id' => $currentUser->id, 'referrals' => $currentUser->total_referrals, 'created' => $currentUser->created_at],
        ]);

        $response = $this->actingAs($currentUser)
            ->getJson('/api/referral');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['name'],
                'referral_code',
                'total_referrals',
                'referral_balance',
                'referrals',
                'leaderboard' => [
                    'top_three' => [
                        '*' => ['id', 'name', 'referral_code', 'total_referrals', 'referral_balance']
                    ],
                    'relative_leaderboard' => [
                        'user_rank',
                        'total_users',
                        'users' => [
                            '*' => ['rank', 'name', 'referral_code', 'total_referrals', 'referral_balance', 'is_current_user']
                        ]
                    ]
                ]
            ]);

        // Log the full response for debugging
        Log::info('API Response Data:', $response->json());

        // Verify top three are in correct order (highest referrals first)
        $topThree = $response->json('leaderboard.top_three');
        Log::info('Top Three Users:', $topThree);
        
        $this->assertCount(3, $topThree);
        $this->assertEquals(20, $topThree[0]['total_referrals']); // user3
        $this->assertEquals(15, $topThree[1]['total_referrals']); // user2
        $this->assertEquals(12, $topThree[2]['total_referrals']); // currentUser

        // Verify relative leaderboard contains current user
        $relativeLeaderboard = $response->json('leaderboard.relative_leaderboard');
        Log::info('Relative Leaderboard:', $relativeLeaderboard);
        
        $this->assertNotNull($relativeLeaderboard['user_rank']);
        
        // Find current user in relative leaderboard
        $currentUserInLeaderboard = collect($relativeLeaderboard['users'])
            ->firstWhere('is_current_user', true);
            
        Log::info('Current User in Leaderboard:', [$currentUserInLeaderboard]);
            
        $this->assertNotNull($currentUserInLeaderboard);
        $this->assertEquals($currentUser->name, $currentUserInLeaderboard['name']);
        $this->assertEquals(12, $currentUserInLeaderboard['total_referrals']);
    }

    /** @test */
    public function it_handles_users_with_same_referral_count_correctly()
    {
        // Create users with same referral count but different creation dates
        $olderUser = User::factory()->create([
            'total_referrals' => 10,
            'created_at' => now()->subDays(10)
        ]);

        $newerUser = User::factory()->create([
            'total_referrals' => 10,
            'created_at' => now()->subDays(5)
        ]);

        $currentUser = User::factory()->create([
            'total_referrals' => 5,
            'created_at' => now()->subDays(1)
        ]);

        Log::info('Tie-breaker Test Users:', [
            'olderUser' => ['id' => $olderUser->id, 'referrals' => $olderUser->total_referrals, 'created' => $olderUser->created_at],
            'newerUser' => ['id' => $newerUser->id, 'referrals' => $newerUser->total_referrals, 'created' => $newerUser->created_at],
            'currentUser' => ['id' => $currentUser->id, 'referrals' => $currentUser->total_referrals, 'created' => $currentUser->created_at],
        ]);

        $response = $this->actingAs($currentUser)
            ->getJson('/api/referral');

        $response->assertStatus(200);

        $topThree = $response->json('leaderboard.top_three');
        Log::info('Tie-breaker Top Three:', $topThree);
        
        // Older user should come first when referral counts are equal
        $this->assertEquals(10, $topThree[0]['total_referrals']);
        $this->assertEquals(10, $topThree[1]['total_referrals']);
        $this->assertEquals($olderUser->name, $topThree[0]['name']);
        $this->assertEquals($newerUser->name, $topThree[1]['name']);
    }

    /** @test */
    public function it_excludes_users_with_zero_referrals_from_leaderboard()
    {
        $userWithReferrals = User::factory()->create([
            'total_referrals' => 5
        ]);

        $userWithZeroReferrals = User::factory()->create([
            'total_referrals' => 0
        ]);

        $currentUser = User::factory()->create([
            'total_referrals' => 3
        ]);

        Log::info('Zero Referrals Test Users:', [
            'userWithReferrals' => ['id' => $userWithReferrals->id, 'referrals' => $userWithReferrals->total_referrals],
            'userWithZeroReferrals' => ['id' => $userWithZeroReferrals->id, 'referrals' => $userWithZeroReferrals->total_referrals],
            'currentUser' => ['id' => $currentUser->id, 'referrals' => $currentUser->total_referrals],
        ]);

        $response = $this->actingAs($currentUser)
            ->getJson('/api/referral');

        $response->assertStatus(200);

        $topThree = $response->json('leaderboard.top_three');
        $relativeLeaderboard = $response->json('leaderboard.relative_leaderboard');

        Log::info('Zero Referrals Response:', [
            'top_three' => $topThree,
            'relative_leaderboard' => $relativeLeaderboard
        ]);

        // Should only include users with referrals > 0
        $this->assertCount(2, $topThree); // userWithReferrals and currentUser
        $this->assertEquals(2, $relativeLeaderboard['total_users']);
        
        // Verify zero-referral user is not in leaderboard
        $userNames = collect($topThree)->pluck('name');
        $this->assertNotContains($userWithZeroReferrals->name, $userNames);
    }

    /** @test */
    public function it_returns_correct_relative_position_for_user()
    {
        // Create multiple users to test relative positioning
        $users = User::factory()->count(10)->create([
            'total_referrals' => fn() => rand(1, 20)
        ]);

        // Sort users by referral count to find expected ranks
        $sortedUsers = $users->sortByDesc('total_referrals')
            ->values();

        $testUser = $sortedUsers->get(4); // User at position 5 (0-based index 4)

        Log::info('Relative Position Test:', [
            'test_user' => ['id' => $testUser->id, 'referrals' => $testUser->total_referrals],
            'all_users_sorted' => $sortedUsers->map(fn($user) => ['id' => $user->id, 'referrals' => $user->total_referrals])->toArray()
        ]);

        $response = $this->actingAs($testUser)
            ->getJson('/api/referral');

        $response->assertStatus(200);

        $relativeLeaderboard = $response->json('leaderboard.relative_leaderboard');
        
        Log::info('Relative Position Response:', $relativeLeaderboard);
        
        // User should be at rank 5 (1-based)
        $this->assertEquals(5, $relativeLeaderboard['user_rank']);
        
        // Should have users around the current user's position
        $this->assertGreaterThanOrEqual(3, count($relativeLeaderboard['users']));
        
        // Current user should be marked correctly
        $currentUserInList = collect($relativeLeaderboard['users'])
            ->firstWhere('is_current_user', true);
        $this->assertNotNull($currentUserInList);
        $this->assertEquals($testUser->name, $currentUserInList['name']);
    }

    /** @test */
    public function it_returns_empty_leaderboard_when_no_users_have_referrals()
    {
        $user = User::factory()->create(['total_referrals' => 0]);

        Log::info('Empty Leaderboard Test:', [
            'user' => ['id' => $user->id, 'referrals' => $user->total_referrals]
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/referral');

        $response->assertStatus(200);

        $topThree = $response->json('leaderboard.top_three');
        $relativeLeaderboard = $response->json('leaderboard.relative_leaderboard');

        Log::info('Empty Leaderboard Response:', [
            'top_three' => $topThree,
            'relative_leaderboard' => $relativeLeaderboard
        ]);

        $this->assertCount(0, $topThree);
        $this->assertNull($relativeLeaderboard['user_rank']);
        $this->assertEquals(0, $relativeLeaderboard['total_users']);
        $this->assertCount(0, $relativeLeaderboard['users']);
    }
}