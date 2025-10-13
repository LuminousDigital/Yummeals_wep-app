# Referral Feature Documentation

Last updated: 2025-10-10

Overview
- The referral feature enables existing users (referrers) to invite new users (referees) via a referral code. When a new user signs up using a valid code, the referrer is credited with a referral bonus. Optionally, the referee receives a welcome bonus.
- The system also recognizes top referrers during specific periods (weekly, monthly, quarterly, yearly) and awards special 100% discount “winner” coupons.
- Additionally, users can receive time-bound free-meal coupons based on daily order milestones (first order of the day, and a probabilistic reward on the 10th order of the day).

Key concepts
- Referrer: Existing user sharing their referral code.
- Referee: New user signing up using a referral code.
- Referral code: Unique, alphanumeric per user.
- Referral bonus: Monetary credit to referrer upon successful referral.
- Welcome bonus: Optional monetary credit to the referee’s wallet.
- Referral balance: Claimable amount for the referrer.
- Winner coupon: Special, time-limited 100% discount coupon awarded to top referrers for each period.

High-level flows
1) Sharing and signup
   - Users share their referral code. Frontend captures it via ?ref=<REFERRAL_CODE> and stores it through the OTP + registration flow.
   - On registration with a valid referral code: link new user to referrer, update counts/balances, write transactions.

2) Viewing and claiming
   - Users view referral stats, leaderboard, referred users list, and can claim referral_balance into their wallet (subject to minimum withdrawal).

3) Periodic winners
   - A scheduled/background command selects top referrers per period and assigns 100% discount winner coupons.

4) Daily free meal coupons
   - Free meal coupons can be issued for the first order of the day, and optionally for the 10th order of the day (probabilistic).

Implementation map
Backend controllers and services
- Frontend referral API: app/Http/Controllers/Frontend/ReferralController.php
  - GET /api/referral → returns user info, referral stats, paginated referred users, leaderboard (top three + relative around user).
  - GET /api/referral/leaderboard → top 20 referrers.
  - GET /api/referral/bonuses → stats + paginated given bonuses.
  - POST /api/referral/claim → moves referral_balance into wallet (validates amount) and records a ReferralTransaction.
  - PUT /api/referral/code → updates referral_code (6–12 alphanumeric, uppercased, unique).

- Signup handling: app/Http/Controllers/Auth/SignupController.php
  - register(): if referral_code present, finds referrer, sets referred_by, assigns new user’s referral_code, updates referrer’s referral_balance and total_referrals, creates ReferralBonus and ReferralTransaction. Optionally credits referee’s wallet per settings.

- Coupon eligibility (free meal):
  - app/Services/MealCouponService.php::handleOrderCoupon(User)
    - Counts today’s orders for the user.
    - Prevents multiple “active winning coupons” per day.
    - First order of the day: if count === 0, issues a 100% Free Meal coupon valid for 30 minutes; returns type: "first_order".
    - Tenth order of the day: if count === 9 and rand(0,1) is true (50% chance), issues a 100% Free Meal coupon valid for 30 minutes; returns type: "tenth_order".
    - Both paths trigger the CouponWon event for notifications.
  - API exposure: routes/api.php
    - GET /api/coupon/bonus/check-eligibility (auth:sanctum) → App\Http\Controllers\Admin\CouponController::checkBonusEligibility calls MealCouponService::handleOrderCoupon and returns details including coupon_code and eligibility_type when applicable.
  - Note: There is also App\Http\Controllers\CouponEligibilityController with a similar check() method not currently wired in routes.

- Winner selection: app/Console/Commands/AssignCouponWinnersCommand.php
  - Command: coupon:assign-winners --type=weekly|monthly|quarterly|yearly|custom [--from=YYYY-MM-DD --to=YYYY-MM-DD]
  - Selects top users by referrals_count in the period window and assigns winner coupons via MealCouponService.

- Winner coupon issuance: app/Services/MealCouponService.php
  - assignWeeklyWinnerCoupon(User):
    - Code pattern: WEEKWINNER-<weekOfMonth>
    - Validity: 2 days; daily_limit_per_user=3; limit_per_user=6; is_reusable=true; discount=100%.
  - assignMonthlyWinnerCoupon(User):
    - Code pattern: MONTHWINNER-<MON>
    - Validity: 30 days; daily_limit_per_user=1; limit_per_user=30; is_reusable=true; discount=100%.
  - assignAllTimeWinnerCoupon(User) used for default/legendary:
    - Code pattern: LEGEND365-<YEAR>
    - Validity: 365 days; daily_limit_per_user=1; limit_per_user=365; is_reusable=true; discount=100%.
  - Duplicate prevention: checks if the user already has a winning coupon by name within the current period’s [start, end] and skips if present.

- Notifications: CouponWon event → SendCouponNotifications listener
  - Event: App\Events\CouponWon
  - Listener: App\Listeners\SendCouponNotifications (queued)
  - Mailable: App\Mail\CouponNotification; template: resources/views/emails/couponNotification.blade.php
  - Sends to winner and to admin (config('mail.admin_address')) with coupon_type, coupon_code, valid_until (displays 100%).

Scheduling
- app/Console/Kernel.php
  - Weekly winners enabled: weeklyOn(1, '0:00')
  - Monthly winners schedule exists but is commented out; uncomment scheduleMonthlyWinners($schedule) to enable.
  - Quarterly and yearly schedules configured.
  - Logs success/failure via TaskLog model entries.

Data model
- Users (migration: 2025_06_05_223515_add_referral_fields_to_users_table.php)
  - referral_code (unique, nullable), referred_by (FK), referral_balance (decimal), total_referrals (int)
- Referral bonuses (migration: 2025_06_05_223446_create_referral_bonuses_table.php)
  - referrer_id, referee_id, amount, currency, status, notes
- Referral transactions (migration: 2025_06_05_223043_create_referral_transactions_table.php)
  - user_id, amount, type (referral_bonus, referral_withdrawal, referral_welcome_bonus), status, meta
- Coupons (migration: 2025_06_30_090340_add_limits_ownership_to_coupons_table.php)
  - daily_limit_per_user (int), is_reusable (bool), is_winning_coupon (bool), user_id (nullable FK)

Models
- User: referral fields and relations
- ReferralBonus: referrer(), referee()
- ReferralTransaction: user(), casts meta
- Coupon: flags for winning coupons; belongsTo user

Frontend integration
- Referral code capture
  - resources/js/components/frontend/auth/SignupRegisterComponent.vue picks up ?ref and stores it; SignupVerifyComponent preserves it through OTP → registration.
- Admin view of referrals
  - resources/js/components/admin/components/buttons/SmReferralsModalComponent.vue displays referred users and their referral codes.
- Coupon eligibility check (free meal)
  - GET /api/coupon/bonus/check-eligibility returns coupon availability/status for authenticated user.

API reference (selected)
- GET /api/referral
- GET /api/referral/leaderboard
- GET /api/referral/bonuses
- POST /api/referral/claim
- PUT /api/referral/code
- GET /api/coupon/bonus/check-eligibility (auth)

Business rules and notes
- Referral code generation: based on username alpha letters (uppercased, 6) + random digits; retries for uniqueness with fallback.
- Settings (Database/Seeders/ReferralSettingsSeeder):
  - referral.signup_bonus (default 10.00 to referrer)
  - referral.referee_bonus (default 5.00 to referee)
  - referral.minimum_withdrawal (default 10.00)
  - referral.is_active, referral.require_kyc
  - referral.tiers (seeded; currently not applied in register())
- Claiming referral balance: server validates min:10 and <= referral_balance; consider aligning min with settings.minimum_withdrawal.
- Free meal daily rules (MealCouponService::handleOrderCoupon):
  - One active winning coupon per user per day; if one exists and still valid, user is told about the existing coupon.
  - First order of the day always rewarded (FREEMEAL-1), validity 30 minutes.
  - Tenth order of the day has a 50% chance (rand(0,1)) to award (FREEMEAL-10), validity 30 minutes.
  - Both paths raise CouponWon event for notifications.
- Winner tie-breakers: command orders by referrals_count desc; consider a secondary deterministic ordering (e.g., created_at asc) for ties.
- Timezone: Coupon windows and scheduling rely on config('app.timezone'); ensure this matches business expectations.

Testing
- tests/Feature/Frontend/ReferralControllerTest.php asserts:
  - Leaderboard structure and ordering; excludes users with zero referrals; relative ranking includes current user.
- Recommendation: Add tests for AssignCouponWinnersCommand and MealCouponService:
  - Verify per-period coupon issuance, duplicate prevention, email notifications, and daily free meal logic (first/10th order) including the random branch on the 10th order.

Operations and maintenance
- To run winner assignment manually:
  - php artisan coupon:assign-winners --type=weekly
  - php artisan coupon:assign-winners --type=monthly
  - php artisan coupon:assign-winners --type=custom --from=YYYY-MM-DD --to=YYYY-MM-DD
- To enable monthly automation, uncomment scheduleMonthlyWinners($schedule) in app/Console/Kernel.php.

