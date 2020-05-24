<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user)
    {
        // Checking for Valid Url Signature
        if (! URL::hasValidSignature($request)) {
            return response()->json(["errors"=>["message"=>"Invalid Verification Link"]],422);
        }

        if ($user->hasVerifiedEmail()) {
             return response()->json(["errors"=>["message"=>"Email Id is Already Verified"]],422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(["message"=>"Email Address is now verified Successfully"]);
    }

    public function resend(Request $request)
    {
        $this->validate($request,[
            'email' => ['email','required']
        ]);

        $user = User::where('email',$request->email)->first();

        if (!$user ) {
            
            return response()->json(["errors"=>["email"=>"Unable to find any user associated with this email address"]],422);
        }

         if ($user->hasVerifiedEmail()) {
             return response()->json(["errors"=>["message"=>"Email Id is Already Verified"]],422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(["status"=>"Verification Email Has Been Sent Successfully"],200);

    }

}
