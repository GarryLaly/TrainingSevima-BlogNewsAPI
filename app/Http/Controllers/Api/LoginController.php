<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\User;
use Newsletter;

class LoginController extends Controller
{
    public function action(LoginRequest $request)
    {
        $user = new User();

        $credentials = $request->only('username', 'password');

        if (auth()->attempt($credentials)) {
            \DB::table('oauth_access_tokens')
                ->where('user_id', auth()->user()->id)
                ->delete();

            auth()->logoutOtherDevices($credentials['password']);
            auth()->attempt($credentials);

            $user = auth()->user();
        }else {
            return response([
                'error' => true,
                'message' => 'Akun tidak terdaftar.',
            ]);
        }

        return (new UserResource($user))->additional([
            'meta' => [
                'token' => $user->createToken('NewsApiToken')->accessToken,
            ]
        ]);
    }
}
