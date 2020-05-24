<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function attemptLogin(Request $request)
    {
        // Attempt to issue a token to the user based on the login credentials

        $token =  $this->guard()->attempt($this->credentials($request));

        if (!$token) {
            
            return false;
            
        }

        // Get the authenticated user

        $user = $this->guard()->user();

        // Cheacking for email Verification

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            
            return false;

        }

        // Set user's bearar token

        $this->guard()->setToken($token);

        return true;
        

    }


    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        // Get the bearer token from the authentication guard(JWT)
        $token = (string)$this->guard()->getToken();

        // extract the expiry date from the token
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token'         => $token,
            'token_type'    => 'bearer',
            'expires_id'    => $expiration
        ]);
    }

    protected function sendFailedLoginResponse()
    {
        $user = $this->guard()->user();

        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            
            return response()->json(["errors"=>[
                "verification"  =>  "You need to verify your account before login"
            ]]);         
        }

        throw ValidationException::withMessages([
            $this->username() => "Oh snap! Credentials doesn't match wit our records"
        ]);
    }
    
    public function logout()
    {
        $this->guard()->logout();
        return response()->json(["message"=>"You are loggedout successfully"]);
    }


}
