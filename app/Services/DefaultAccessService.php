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



// namespace App\Services;

// use App\Models\DefaultAccess;
// use Exception;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;

// class DefaultAccessService
// {
//     /**
//      * @throws Exception
//      */
//     public function show($userId = null): array
//     {
//         try {
//             $uid = $userId ?: Auth::id();
//             Log::debug('DefaultAccessService::show - User ID:', ['user_id' => $uid, 'auth_id' => Auth::id()]);
            
//             $array = [];
//             $defaultAccess = DefaultAccess::where(['user_id' => $uid])->get();
            
//             Log::debug('DefaultAccessService::show - Query result count:', ['count' => $defaultAccess->count()]);
//             Log::debug('DefaultAccessService::show - Query result:', ['data' => $defaultAccess->toArray()]);
            
//             if ($defaultAccess) {
//                 foreach ($defaultAccess as $default) {
//                     $array[$default->name] = $default->default_id;
//                     Log::debug('DefaultAccessService::show - Processing default access:', [
//                         'name' => $default->name, 
//                         'default_id' => $default->default_id
//                     ]);
//                 }
//             }
            
//             Log::debug('DefaultAccessService::show - Final array:', ['result' => $array]);
//             return $array;
//         } catch (Exception $exception) {
//             Log::error('DefaultAccessService::show - Exception occurred:', [
//                 'message' => $exception->getMessage(),
//                 'file' => $exception->getFile(),
//                 'line' => $exception->getLine(),
//                 'user_id' => $userId ?? Auth::id(),
//                 'trace' => $exception->getTraceAsString()
//             ]);
//             throw new Exception($exception->getMessage(), 422);
//         }
//     }

//     /**
//      * @throws Exception
//      */
//     public function storeOrUpdate($request = [], $userId = null): array
//     {
//         try {
//             $uid = $userId ?: Auth::id();
//             Log::debug('DefaultAccessService::storeOrUpdate - Starting:', [
//                 'user_id' => $uid,
//                 'auth_id' => Auth::id(),
//                 'request_data' => $request,
//                 'request_count' => count($request)
//             ]);

//             if (!blank($request)) {
//                 foreach ($request as $key => $item) {
//                     Log::debug('DefaultAccessService::storeOrUpdate - Processing key:', [
//                         'key' => $key,
//                         'value' => $item
//                     ]);
                    
//                     if ($key == 'branch_id') {
//                         Log::debug('DefaultAccessService::storeOrUpdate - Processing branch_id:', [
//                             'original_value' => $item,
//                             'auth_user_branch_id' => Auth::check() ? Auth::user()->branch_id : 'not_authenticated'
//                         ]);
                        
//                         if (Auth::check() && Auth::user()->branch_id != '0') {
//                             $item = Auth::user()->branch_id;
//                             Log::debug('DefaultAccessService::storeOrUpdate - Branch_id overridden:', [
//                                 'new_value' => $item
//                             ]);
//                         }
//                     }
                    
//                     $defaultAccess = DefaultAccess::firstOrNew([
//                         'user_id' => $uid, 
//                         'name' => $key
//                     ]);
                    
//                     Log::debug('DefaultAccessService::storeOrUpdate - DefaultAccess model:', [
//                         'exists' => $defaultAccess->exists,
//                         'current_default_id' => $defaultAccess->default_id,
//                         'new_default_id' => $item
//                     ]);
                    
//                     $defaultAccess->default_id = $item;
//                     $saveResult = $defaultAccess->save();
                    
//                     Log::debug('DefaultAccessService::storeOrUpdate - Save result:', [
//                         'key' => $key,
//                         'save_success' => $saveResult,
//                         'saved_id' => $defaultAccess->id
//                     ]);
//                 }
                
//                 Log::debug('DefaultAccessService::storeOrUpdate - All items processed');
//             } else {
//                 Log::warning('DefaultAccessService::storeOrUpdate - Empty request received');
//             }
            
//             // Call show method to return updated data
//             $result = $this->show($uid);
//             Log::debug('DefaultAccessService::storeOrUpdate - Final result:', ['result' => $result]);
            
//             return $result;
//         } catch (Exception $exception) {
//             Log::error('DefaultAccessService::storeOrUpdate - Exception occurred:', [
//                 'message' => $exception->getMessage(),
//                 'file' => $exception->getFile(),
//                 'line' => $exception->getLine(),
//                 'user_id' => $userId ?? Auth::id(),
//                 'request_data' => $request,
//                 'trace' => $exception->getTraceAsString()
//             ]);
//             throw new Exception($exception->getMessage(), 422);
//         }
//     }
// }