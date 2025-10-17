<?php

namespace App\Services;

use App\Models\DefaultAccess;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DefaultAccessService
{
    /**
     * @throws Exception
     */
    public function show($userId = null): array
    {
        try {
            $uid = $userId ?: Auth::id();
            $array = [];
            $defaultAccess = DefaultAccess::where(['user_id' => $uid])->get();
            if ($defaultAccess) {
                foreach ($defaultAccess as $default) {
                    $array[$default->name] = $default->default_id;
                }
            }
            return $array;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function storeOrUpdate($request = [], $userId = null): array
    {
        try {
            $uid = $userId ?: Auth::id();
            if (!blank($request)) {
                foreach ($request as $key => $item) {
                    if ($key == 'branch_id') {
                        if (Auth::check() && Auth::user()->branch_id != '0') {
                            $item = Auth::user()->branch_id;
                        }
                    }
                    $defaultAccess = DefaultAccess::firstOrNew(['user_id' => $uid, 'name' => $key]);
                    $defaultAccess->default_id = $item;
                    $defaultAccess->save();
                }
            }
            return $this->show($uid);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
