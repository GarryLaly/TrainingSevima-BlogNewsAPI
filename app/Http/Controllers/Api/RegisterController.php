<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\User;
use Newsletter;

class RegisterController extends Controller
{
    public function action(RegisterRequest $request)
    {
        $usernameExist = User::where('username', $request->username)->first();
        if($usernameExist) {
            return response()->json([
                'error' => true,
                'message' => 'Username sudah digunakan',
            ]);
        }

        $user = new User();
        $registeredUser = $user->registerUser($request);

        $token = $registeredUser->createToken('NewsApiToken')->accessToken;

        return (new UserResource($registeredUser))->additional([
            'meta' => [
                'token' => $token,
            ]
        ]);
    }
}
