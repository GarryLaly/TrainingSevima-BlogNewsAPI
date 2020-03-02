<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function registerUser(Request $request)
    {
        return DB::transaction(function() use ($request){
            $additional = [
                'password' => bcrypt($request->password),
            ];

            $user = $this->create(array_merge(
                $request->only(
                    'username',
                    'password'
                ), $additional
            ));

            return $user;
        });
    }
}
