<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Routing\Controller as BaseController;
use Exception;
class Controller extends BaseController
{
    /**
     * @param string $message
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = [], $message = 'success', $code = 0)
    {
        return $this->outPut($code, $message, $data);
    }

    /**
     * @param int $code
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail($code, $message = '', $data = [])
    {
        if(!$message && array_key_exists($code, config('errorCode.code'))){
            $message = config('errorCode.code')[(int) $code];
        }
        return $this->outPut($code, $message, $data);
    }

    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function outPut($code, $message, $data = [])
    {
        $expiresAt = Carbon::now()->addMinute();
        $userId = Auth::id() ?? Cache::get('userId');

        Cache::put('errorCode_' . $userId, $code, $expiresAt);
        Cache::put('message_' . $userId, $message, $expiresAt);

        return response()->json([
            'errorCode' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 根据异常码抛出异常
     * 
     * @param int $code: 异常码
     * @throws Exeption
     */
    public function throwExeptionByCode($code)
    {
        $message = 'Default exeption message';
        if(array_key_exists($code, config('errorCode.code'))){
            $message = config('errorCode.code')[(int) $code];
        }
        throw new Exception($message, $code);
    }
}
