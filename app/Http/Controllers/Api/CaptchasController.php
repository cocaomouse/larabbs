<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CaptchaRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Str;

class CaptchasController extends Controller
{
    /**
     * 生成图片验证码
     *
     * @param CaptchaRequest $request
     * @param CaptchaBuilder $captchaBuilder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha_' . Str::random(15);
        $captchaCode = $captchaBuilder->build();
        $expiredAt = now()->addMinute(5);
        $phone = $request->phone;

        \Cache::put($key, ['phone' => $phone,'code' => $captchaCode->getPhrase()], $expiredAt);

        return response()->json([
            'captcha_key' => $key,
            'captcha_code' => $captchaCode->inline(),
            'expired_at' => $expiredAt->toDateTimeString(),
        ]);
    }
}
