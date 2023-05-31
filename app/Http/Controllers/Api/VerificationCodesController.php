<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;

class VerificationCodesController extends Controller
{
    /**
     * 短信验证码
     *
     * @param EasySms $easySms
     * @param VerificationCodeRequest $request
     */
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = \Cache::get($request->captcha_key);
        if (!$captchaData) {
            abort(403, '图形验证码已失效');
        }

        if (!hash_equals(strtolower($captchaData['code']), $request->captcha_code)) {
            // 删除图形验证码
            \Cache::forget($request->captcha_key);

            throw new AuthenticationException('图形验证码错误');
        }
        $phone = $captchaData['phone'];

        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            // 生成四位code
            $code = str_pad(random_int(1, 999), 4, 0, STR_PAD_LEFT);

            try {
                $result = $easySms->send($phone, [
                   'template' => config('easysms.gateways.aliyun.templates.register'),
                   'data' => [
                       'code' => $code,
                   ],
               ]);
            } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                abort(500, $message ?: '短信发送异常');
            }
        }

        $key = 'verificationCode_' . Str::random(15);
        $expiredAt = now()->addMinute(6);

        \Cache::put($key, ['phone' => $phone,'code' => $code], $expiredAt);
        \Cache::forget($request->captcha_key);

        return response()->json([
           'key' => $key,
           'expired_at' => $expiredAt->toDateTimeString(),
       ])->setStatusCode(201);
    }
}
