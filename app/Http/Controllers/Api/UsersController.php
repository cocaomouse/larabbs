<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\Image;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * 获取当前登录用户信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return (new UserResource($request->user()))->showSensitiveFields();
    }

    /**
     * 小程序注册新用户
     *
     * @param UserRequest $request
     * @return array
     */
    public function weappStore(UserRequest $request)
    {
        // 缓存中是否存在对应的key
        $verifyData = \Cache::get($request->verification_key);
        if (!$verifyData) {
            abort(403, '验证码已失效');
        }

        // 判断验证码是否相等，不相等返回401错误
        if (!hash_equals((string)$verifyData['code'], $request->verification_code)) {
            throw new AuthenticationException('验证码错误');
        }

        // 根据code获取微信openid和session_key(通过overtrue/laravel-wechat扩展包获取)
        $miniProgram = app('easywechat.mini_program');
        $data = $miniProgram->auth->session($request->code);

        // 如果结果错误,说明code已过期或不正确,返回401错误
        if (isset($data['errcode'])) {
            throw new AuthenticationException('code 不正确');
        }

        // 如果openid对应的用户已存在，报错403
        $user = User::where('weapp_openid', $data['openid'])->first();

        if ($user) {
            throw new AuthenticationException('微信已绑定其他用户，请直接登录');
        }

        // 创建用户
        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => $request->password,
            'weapp_openid' => $data['openid'],
            'weixin_session_key' => $data['session_key'],
        ]);

        return (new UserResource(($user)))->showSensitiveFields();
    }

    /**
     * 编辑用户个人信息
     *
     * @param UserRequest $request
     * @param ImageUploadHandler $uploadHandler
     * @return array
     */
    public function update(UserRequest $request, ImageUploadHandler $uploadHandler)
    {
        $user = $request->user();
        $attributes = $request->only(['name', 'email', 'introduction','registration_id']);

        if ($request->avatar_image_id) {
            $image = Image::find($request->avatar_image_id);
            $attributes['avatar'] = $image->path;
        }

        $user->update($attributes);

        return (new UserResource($user))->showSensitiveFields();
    }
}
