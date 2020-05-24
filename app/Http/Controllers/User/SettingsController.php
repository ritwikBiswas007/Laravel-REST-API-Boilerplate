<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Rules\CheckSamePassword;
use App\Rules\MatchOldPassword;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

      
        
        $this->validate($request, [
            'tagline'=> 'required',
            'formatted_address'=> 'required',
            'name'=> 'required',
            'location.latitude'=> ['required','numeric','min:-90','max:90'],
            'location.longitude'=> ['required','numeric','min:-180','max:180'],
            'about'=> ['required','min:30','max:500'],
            'available_to_hire'=>['required']
            ]);
            
        $location = new Point($request->location['latitude'],$request->location['longitude'],4326);

            
            $user->update([
            'tagline'=> $request->tagline,
            'formatted_address'=> $request->formatted_address,
            'name'=> $request->name,
            'location' => $location,
            'about'=> $request->about,
            'available_to_hire'=>$request->available_to_hire
        ]);

        return new UserResource($user);

    }
    
    public function updatePassword(Request $request)
    {
        
        $this->validate($request, [
            'current_password'=> ['required', new MatchOldPassword],
            'password'=> ['required','confirmed', 'min:6',  new CheckSamePassword]
            ]);


        $request->user()->fill([
            'password' => Hash::make($request->password)
        ])->save();

        return response()->json(["message"=>"Your Password Has Been Updated Successfully"]);
        
    }
}
