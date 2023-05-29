<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    /**
     * 获取当前登录用户信息
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return (new UserResource($request->user()))->showSensitiveFields();
    }
}
