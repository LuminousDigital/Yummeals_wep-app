<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Libraries\AppLibrary;
use App\Models\User;
use App\Services\DefaultAccessService;
use App\Services\MenuService;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Laravel\Socialite\Two\InvalidStateException;
use Smartisan\Settings\Facades\Settings;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SocialLoginController extends Controller
{
    public function __construct(
        private MenuService $menuService,
        private PermissionService $permissionService,
        private DefaultAccessService $defaultAccessService,
    ) {}

    /**
     * Build configured Socialite driver per provider.
     */
    private function buildDriver(string $provider)
    {
        if ($provider === 'facebook') {
            return Socialite::driver('facebook')
                ->scopes(['email'])
                ->fields(['name','first_name','last_name','email'])
                ->with(['auth_type' => 'rerequest']);
        }
        if ($provider === 'google') {
            return Socialite::driver('google')
                ->with(['access_type' => 'offline', 'prompt' => 'consent']);
        }
        return Socialite::driver($provider);
    }

    /**
     * Compute frontend base URL for redirect.
     */
    private function frontendBase(): string
    {
        return rtrim((string) (env('SOCIAL_LOGIN_REDIRECT') ?: config('app.url') ?: request()->getSchemeAndHttpHost()), '/');
    }

    /**
     * Unified redirect handler.
     */
    private function redirect(string $provider): RedirectResponse
    {
        $driver = $this->buildDriver($provider);

        // If platform param present (e.g., platform=mobile), carry across via a short-lived cookie
        $platform = request()->query('platform');
        if ($platform) {
            try { Cookie::queue(cookie('social_platform', (string) $platform, 5)); } catch (\Throwable $e) {}
        }

        $response = $driver->redirect();
        try {
            Log::info("SocialLogin: {$provider} redirect", [
                'target' => method_exists($response, 'getTargetUrl') ? $response->getTargetUrl() : null,
                'services' => [
                    'client_id_set' => (bool) config("services.{$provider}.client_id"),
                    'redirect' => config("services.{$provider}.redirect"),
                ],
                'platform' => $platform,
            ]);
        } catch (\Throwable $e) {}
        return $response;
    }
    /**
     * Unified callback handler.
     */
    private function callback(string $provider)
    {
        try {
            $driver = $this->buildDriver($provider);
            try {
                /** @var SocialiteUser $provUser */
                $provUser = $driver->user();
            } catch (InvalidStateException $invalid) {
                Log::warning("SocialLogin: {$provider} invalid state, retrying stateless", [
                    'query' => request()->only(['state','code','error','error_description']),
                    'message' => $invalid->getMessage(),
                ]);
                $provUser = $driver->stateless()->user();
            }

            if (!$provUser) {
                throw new \RuntimeException("No {$provider} user from provider");
            }

            Log::info("SocialLogin: {$provider} user received", [
                'id' => $provUser->getId(),
                'email_present' => (bool) $provUser->getEmail(),
                'name_present' => (bool) $provUser->getName(),
                'token_present' => !empty($provUser->token),
                'refresh_present' => !empty($provUser->refreshToken ?? null),
            ]);

            $user = $this->upsertSocialUser($provider, $provUser);
            $payload = $this->buildAuthPayload($user);

            if (request()->boolean('json')) {
                return new JsonResponse($payload, 201);
            }

            $front = $this->frontendBase();

            // If platform cookie indicates mobile, redirect to home with #mobile payload
            $platform = (string) (request()->query('platform') ?: request()->cookie('social_platform', ''));
            if (in_array(strtolower($platform), ['mobile','app'], true)) {
                // Build fields availability snapshot
                $name = trim((string) $user->name);
                $parts = preg_split('/\s+/', $name);
                $first = trim((string) ($parts[0] ?? ''));
                $last = trim((string) (isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : ''));
                $available = [
                    'first_name' => $first,
                    'last_name'  => $last,
                    'email'      => (string) ($user->email ?? ''),
                    'phone'      => (string) ($user->phone ?? ''),
                    'country_code' => (string) ($user->country_code ?? ''),
                ];
                $missing = [];
                foreach ($available as $k => $v) { if (trim((string) $v) === '') { $missing[] = $k; } }
                $payload['profile_fields'] = [ 'available' => $available, 'missing' => $missing ];

                $b64 = base64_encode(json_encode($payload));
                try { Cookie::queue(Cookie::forget('social_platform')); } catch (\Throwable $e) {}
                Log::info("SocialLogin: mobile flag present, redirecting to home with mobile hash", [ 'platform' => $platform ]);
                return redirect()->to($front . '/home#mobile=' . urlencode($b64));
            }

            $needsUpdate = $this->needsProfileUpdate($user);
            $dest = $needsUpdate ? '/edit-profile' : '/login';
            Log::info("SocialLogin: redirecting {$provider} auth to frontend", [
                'base' => $front,
                'dest' => $dest,
                'needs_update' => $needsUpdate,
            ]);
            $b64 = base64_encode(json_encode($payload));
            return redirect()->to($front . $dest . '#social=' . urlencode($b64));
        } catch (\Throwable $e) {
            Log::error(ucfirst($provider).' authentication failed', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'query' => request()->only(['state','code','error','error_description']),
                'services_redirect' => config("services.{$provider}.redirect"),
            ]);
            return new JsonResponse(['errors' => ['social' => ucfirst($provider).' authentication failed']], 400);
        }
    }

    public function redirectToGoogle(): RedirectResponse
    {
        return $this->redirect('google');
    }

    public function handleGoogleCallback()
    {
        return $this->callback('google');
    }

    public function redirectToFacebook(): RedirectResponse
    {
        return $this->redirect('facebook');
    }

    public function handleFacebookCallback()
    {
        return $this->callback('facebook');
    }

    private function upsertSocialUser(string $provider, SocialiteUser $s): User
    {
        $email = $s->getEmail();
        $raw = is_array($s->user ?? null) ? $s->user : [];
        $name = null;
        if ($provider === 'google') {
            $given = $raw['given_name'] ?? null;
            $family = $raw['family_name'] ?? null;
            $name = trim(trim(($given ?? '') . ' ' . ($family ?? '')));
        } elseif ($provider === 'facebook') {
            $first = $raw['first_name'] ?? null;
            $last = $raw['last_name'] ?? null;
            $name = trim(trim(($first ?? '') . ' ' . ($last ?? '')));
        }
        if (empty($name)) {
            $name = $s->getName() ?: ($s->getNickname() ?: Str::title($provider.' user'));
        }
        $id = (string) $s->getId();
        $token = $s->token ?? ($s->accessTokenResponseBody['access_token'] ?? null);
        $refresh = $s->refreshToken ?? ($s->accessTokenResponseBody['refresh_token'] ?? null);

        $lookup = $email ? ['email' => $email] : [$provider.'_id' => $id];

        $data = [
            'name' => $name,
            'email' => $email,
        ];

        if ($provider === 'google') {
            $data['google_id'] = $id;
            $data['google_token'] = $token;
            $data['google_refresh_token'] = $refresh;
        } else {
            $data['facebook_id'] = $id;
            $data['facebook_token'] = $token;
            $data['facebook_refresh_token'] = $refresh;
        }

        $base = Str::slug($name) ?: ($provider.'-'.$id);
        $createDefaults = [
            'username' => $this->uniqueUsername($base),
            'password' => bcrypt(Str::random(12)),
            'referral_code' => $this->ensureReferralCode($data),
        ];
        $defaultBranch = Settings::group('site')->get('site_default_branch');
        if ($defaultBranch) {
            $createDefaults['branch_id'] = (int) $defaultBranch;
        }

        /** @var User $user */
        $user = User::firstOrCreate($lookup, array_merge($data, $createDefaults));

        $user->fill($data);
        // Ensure referral code (derived solely from $data) if still missing after fill
        if (empty($user->referral_code)) {
            try {
                $code = $this->ensureReferralCode($data);
                if (!empty($code)) {
                    $user->referral_code = $code;
                }
            } catch (\Throwable $e) {
                Log::notice('SocialLogin: referral code generation failed', [
                    'user_id' => $user->id ?? null,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        Log::info('SocialLogin: upsert user', [
            'provider' => $provider,
            'provider_id' => $id,
            'lookup' => array_keys($lookup),
            'user_id' => $user->id ?? null,
            'email_present' => (bool) $email,
        ]);

        if (empty($user->email_verified_at)) {
            $user->email_verified_at = now();
        }
        if ((int) $user->status !== Status::ACTIVE) {
            $user->status = Status::ACTIVE;
        }
        $user->save();

        try {
            if (!$user->roles()->exists()) {
                $user->assignRole('Customer');
            }
        } catch (\Throwable $e) {
            Log::notice('SocialLogin: role assignment failed', ['user_id' => $user->id, 'message' => $e->getMessage()]);
        }

        return $user;
    }

    private function buildAuthPayload(User $user): array
    {
        $branchId = $user->branch_id ?: Settings::group('site')->get('site_default_branch');
        if ($branchId) {
            $this->defaultAccessService->storeOrUpdate(['branch_id' => $branchId], $user->id);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $permission = PermissionResource::collection($this->permissionService->permission($user->roles[0] ?? null));
        $menus = MenuResource::collection(collect($this->menuService->menu($user->roles[0] ?? null)));
        $defaultPermission = AppLibrary::defaultPermission($permission);
        $defaultMenu = (object) AppLibrary::defaultMenu($this->menuService->menu($user->roles[0] ?? null), $defaultPermission);

        return [
            'message' => trans('all.message.login_success'),
            'token' => $token,
            'branch_id' => (int) $user->branch_id,
            'user' => (new UserResource($user))->resolve(),
            'menu' => $menus->toArray(request()),
            'permission' => $permission->toArray(request()),
            'defaultPermission' => $defaultPermission,
            'defaultMenu' => $defaultMenu,
        ];
    }

    private function needsProfileUpdate(User $user): bool
    {
        $name = trim((string) $user->name);
        $parts = preg_split('/\s+/', $name);
        $firstMissing = empty($parts[0] ?? null);
        $lastMissing = empty($parts[1] ?? null);
        return empty($user->email) || empty($user->phone) || $firstMissing || $lastMissing;
    }

    /**
     * Ensure a referral code derived solely from the given data array.
     * Prefers name, then email local-part, then username, otherwise falls back to provider id info.
     */
    private function ensureReferralCode(array $data): string
    {
        $base = '';
        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $username = trim((string) ($data['username'] ?? ''));

        if ($name !== '') {
            $base = $name;
        } elseif ($email !== '') {
            $base = explode('@', $email, 2)[0] ?: $email;
        } elseif ($username !== '') {
            $base = $username;
        } elseif (!empty($data['google_id'] ?? null)) {
            $base = 'google-'.$data['google_id'];
        } elseif (!empty($data['facebook_id'] ?? null)) {
            $base = 'facebook-'.$data['facebook_id'];
        } else {
            $base = 'user';
        }

        try {
            $code = User::generateUniqueReferralCode($base);
            Log::info('SocialLogin: referral code ensured', [
                'base' => $base,
                'code_len' => strlen($code),
            ]);
            return $code;
        } catch (\Throwable $e) {
            Log::notice('SocialLogin: referral code generation exception', [
                'base' => $base,
                'message' => $e->getMessage(),
            ]);
            return '';
        }
    }

    private function uniqueUsername(string $base): string
    {
        $candidate = $base;
        $i = 1;
        while (User::where('username', $candidate)->exists()) {
            $candidate = $base.'-'.$i;
            $i++;
            if ($i > 100) {
                $candidate = $base.'-'.Str::random(5);
                break;
            }
        }
        return $candidate;
    }
}
