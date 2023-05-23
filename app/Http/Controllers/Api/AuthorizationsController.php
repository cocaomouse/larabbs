<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Models\User;

class AuthorizationsController extends Controller
{
    /**
     * 微信小程序登录
     *
     * @param WeappAuthorizationRequest $request
     * @return int
     */
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        // 根据code获取微信openid和session_key(通过overtrue/laravel-wechat扩展包获取)
        $miniProgram = app('easywechat.mini_program');
        $data = $miniProgram->auth->session($code);

        // 如果结果错误,说明code已过期或不正确,返回401错误
        if (isset($data['errcode'])) {
            throw new AuthenticationException('code 不正确');
        }

        // 找到openid对应的用户
        $user = User::where('weapp_openid', $data['openid'])->first();
        $attributes['weixin_session_key'] = $data['session_key'];
        $attributes['weapp_openid'] = $data['openid'];

        // 未找到对应用户则需要提交用户名密码进行用户绑定
        if (!$user) {
            // 如果未提交用户名密码，403错误提示
            if (!$request->username) {
                throw new AuthenticationException('用户不存在');
            }
            $username = $request->username;

            // 用户名可以是邮箱或电话
            filter_var($username, FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $username :
                $credentials['phone'] = $username;
            $credentials['password'] = $request->password;

            // 验证用户名和密码是否正确
            if (!auth('api')->once($credentials)) {
                throw new AuthenticationException('用户名或密码错误');
            }

            // 获取对应的用户
            $user = auth('api')->getUser();
        }

        // 更新用户数据
        $user->update($attributes);

        // 为对应用户创建 JWT
        $token = auth('api')->login($user);

        return $this->respondWithToken($token)->setStatusCode(201);

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
