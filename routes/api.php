<?php

use App\Http\Controllers\Api\AuthorizationsController;
use App\Http\Controllers\Api\CaptchasController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\VerificationCodesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::prefix('v1')->namespace('Api')->middleware('change-locale')->name('api.v1.')->group(function () {
    Route::middleware('throttle:' . config('api.rate_limits.sign'))->group(function () {
        // 图片验证码
        Route::post('captchas', [CaptchasController::class,'store'])
            ->name('captchas.store');
        // 短信验证码
        Route::post('verificationCodes', [VerificationCodesController::class,'store'])
            ->name('verificationCodes.store');
        // 登录
        Route::post('authorizations', [AuthorizationsController::class, 'store'])
            ->name('authorizations.store');
        // 小程序登录
        Route::post('weapp/authorizations', [AuthorizationsController::class, 'weappStore'])
            ->name('weapp.authorizations.store');
        // 小程序注册
        Route::post('weapp/users', [UsersController::class,'weappStore'])
            ->name('weapp.users.store');
        // 刷新小程序登录token
        Route::put('authorizations/current', [AuthorizationsController::class, 'update'])
            ->name('authorizations.update');
        // 删除token 退出登录
        Route::delete('authorizations/current', [AuthorizationsController::class,'destroy'])
            ->name('authorizations.destroy');
    });
    Route::middleware('throttle:' . config('api.rate_limits.access'))->group(function () {
        // 登录后可以访问的接口
        Route::middleware('auth:api')->group(function () {
            // 当前登录用户信息
            Route::get('user', [UsersController::class,'me'])->name('user.show');
        });
    });
});
